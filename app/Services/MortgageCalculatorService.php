<?php

namespace App\Services;

use App\DTOs\LoanDTO;
use App\Models\Loan;
use App\Models\LoanAmortizationSchedule;
use App\Models\ExtraRepaymentSchedule;

class MortgageCalculatorService
{
    public function calculateLoan(
        LoanDTO $loanDTO
    ): Loan {
        $monthlyPayment = $this->calculateMonthlyPayment(
            $loanDTO->loanAmount,
            $loanDTO->annualInterestRate,
            $loanDTO->loanTermYears
        );

        $loan = Loan::create([
            'loan_amount' => $loanDTO->loanAmount,
            'annual_interest_rate' => $loanDTO->annualInterestRate,
            'loan_term_years' => $loanDTO->loanTermYears,
            'monthly_extra_payment' => $loanDTO->monthlyExtraPayment,
            'monthly_payment' => $monthlyPayment,
            'original_term_months' => $loanDTO->loanTermYears * 12
        ]);

        $this->generateAmortizationSchedule($loan);

        if ($loanDTO->monthlyExtraPayment > 0) {
            $this->generateExtraPaymentSchedule($loan);
        }

        $this->calculateLoanTotals($loan);

        return $loan->fresh();
    }

    private function calculateMonthlyPayment(float $loanAmount, float $annualRate, int $years): float
    {
        $monthlyRate = ($annualRate / 12) / 100;
        $numberOfMonths = $years * 12;

        if ($monthlyRate == 0) {
            return $loanAmount / $numberOfMonths;
        }

        $monthlyPayment = ($loanAmount * $monthlyRate) /
            (1 - pow(1 + $monthlyRate, -$numberOfMonths));

        return round($monthlyPayment, 2);
    }

    private function generateAmortizationSchedule(Loan $loan): void
    {
        $balance = $loan->loan_amount;
        $monthlyRate = $loan->monthly_interest_rate;
        $monthlyPayment = $loan->monthly_payment;
        $maxMonths = $loan->total_months * 2;

        for ($month = 1; $month <= $maxMonths && $balance > 0; $month++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = min($monthlyPayment - $interestPayment, $balance);
            $endingBalance = $balance - $principalPayment;

            LoanAmortizationSchedule::create([
                'loan_id' => $loan->id,
                'month_number' => $month,
                'starting_balance' => $balance,
                'monthly_payment' => $monthlyPayment,
                'principal_component' => $principalPayment,
                'interest_component' => $interestPayment,
                'ending_balance' => max(0, $endingBalance)
            ]);

            $balance = max(0, $endingBalance);
        }
    }

    private function generateExtraPaymentSchedule(Loan $loan): void
    {
        $balance = $loan->loan_amount;
        $monthlyRate = $loan->monthly_interest_rate;
        $monthlyPayment = $loan->monthly_payment;
        $extraPayment = $loan->monthly_extra_payment;

        for ($month = 1; $month <= $loan->total_months * 2 && $balance > 0; $month++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            if ($balance < $monthlyPayment) {
                $principalPayment = $balance;
                $monthlyPayment = $balance + $interestPayment;
            }
            $balanceAfterRegular = $balance - $principalPayment;

            $actualExtraPayment = min($extraPayment, max(0, $balanceAfterRegular));
            $endingBalance = max(0, $balanceAfterRegular - $actualExtraPayment);

            $remainingTerm = $this->calculateRemainingTerm($endingBalance, $monthlyRate, $monthlyPayment);

            ExtraRepaymentSchedule::create([
                'loan_id' => $loan->id,
                'month_number' => $month,
                'starting_balance' => $balance,
                'monthly_payment' => $monthlyPayment,
                'principal_component' => $principalPayment,
                'interest_component' => $interestPayment,
                'extra_repayment' => $actualExtraPayment,
                'ending_balance_after_extra' => $endingBalance,
                'remaining_term_months' => $remainingTerm
            ]);

            $balance = $endingBalance;
        }
    }

    private function calculateRemainingTerm(float $balance, float $monthlyRate, float $monthlyPayment): int
    {
        if ($monthlyRate <= 0 || $monthlyPayment <= $balance * $monthlyRate) {
            return 999; // Payment is too low
        }

        $numerator = log($monthlyPayment / ($monthlyPayment - $monthlyRate * $balance));
        $denominator = log(1 + $monthlyRate);

        $n = $numerator / $denominator;

        return (int)ceil($n);
    }

    private function calculateLoanTotals(Loan $loan): void
    {
        $standardSchedule = $loan->amortizationSchedule;
        $totalPayments = $standardSchedule->sum('monthly_payment');
        $totalInterest = $standardSchedule->sum('interest_component');

        $extraSchedule = $loan->extraRepaymentSchedule;
        $actualTermMonths = $loan->total_months;
        $interestSaved = 0;
        $timeSavedMonths = 0;
        $effectiveRate = $loan->annual_interest_rate;

        if ($extraSchedule->count() > 0) {
            $actualTermMonths = $extraSchedule->where('ending_balance_after_extra', '>', 0)->count() + 1;
            $totalInterestWithExtra = $extraSchedule->sum('interest_component');
            $interestSaved = $totalInterest - $totalInterestWithExtra;
            $timeSavedMonths = $loan->total_months - $actualTermMonths;

            $totalExtraPayments = $extraSchedule->sum('extra_repayment');
            $totalPaid = $totalInterestWithExtra + $loan->loan_amount + $totalExtraPayments;
            $effectiveRate = $this->calculateEffectiveRate($loan->loan_amount, $totalPaid, $actualTermMonths);
        }

        $loan->update([
            'total_payments' => $totalPayments,
            'total_interest' => $totalInterest,
            'actual_term_months' => $actualTermMonths,
            'time_saved_months' => $timeSavedMonths,
            'interest_saved' => $interestSaved,
            'effective_interest_rate' => $effectiveRate
        ]);
    }

    private function calculateEffectiveRate(float $principal, float $totalPaid, int $months): float
    {
        if ($principal <= 0 || $months <= 0 || $totalPaid <= 0) {
            return 0;
        }

        $monthlyPayment = $totalPaid / $months;

        $low = 0.000001;
        $high = 1;
        $tolerance = 0.0000001;

        while ($high - $low > $tolerance) {
            $mid = ($low + $high) / 2;

            $guessPayment = ($mid * $principal) / (1 - pow(1 + $mid, -$months));

            if ($guessPayment > $monthlyPayment) {
                $high = $mid;
            } else {
                $low = $mid;
            }
        }

        $monthlyRate = ($low + $high) / 2;
        $annualRate = $monthlyRate * 12;

        return round($annualRate * 100, 4);
    }
}
