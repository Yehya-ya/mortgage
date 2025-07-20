<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $loanAmount = fake()->numberBetween(50000, 1000000);
        $annualRate = fake()->randomFloat(2, 2.5, 8.5);
        $termYears = fake()->numberBetween(10, 30);
        $extraPayment = fake()->optional(0.3)->numberBetween(50, 1000) ?? 0;

        $monthlyRate = ($annualRate / 12) / 100;
        $numberOfMonths = $termYears * 12;
        
        $monthlyPayment = $monthlyRate > 0 
            ? ($loanAmount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$numberOfMonths))
            : $loanAmount / $numberOfMonths;

        return [
            'loan_amount' => $loanAmount,
            'annual_interest_rate' => $annualRate,
            'loan_term_years' => $termYears,
            'monthly_extra_payment' => $extraPayment,
            'monthly_payment' => round($monthlyPayment, 2),
            'original_term_months' => $numberOfMonths,
            'effective_interest_rate' => $annualRate,
            'actual_term_months' => $numberOfMonths,
            'time_saved_months' => 0,
            'interest_saved' => 0
        ];
    }
}