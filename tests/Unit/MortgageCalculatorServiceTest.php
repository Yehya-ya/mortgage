<?php

namespace Tests\Unit;

use App\DTOs\LoanDTO;
use App\Services\MortgageCalculatorService;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MortgageCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private MortgageCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MortgageCalculatorService();
    }

    public function test_calculates_monthly_payment_correctly()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 30,
            'monthly_extra_payment' => 0
        ]);

        $loan = $this->service->calculateLoan($data);

        $this->assertEquals(1520.06, $loan->monthly_payment);
    }

    public function test_generates_amortization_schedule()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 100000,
            'annual_interest_rate' => 5.0,
            'loan_term_years' => 10,
            'monthly_extra_payment' => 0
        ]);

        $loan = $this->service->calculateLoan($data);

        $this->assertDatabaseCount('loan_amortization_schedules', 120);
        
        $firstPayment = $loan->amortizationSchedule()->where('month_number', 1)->first();
        $this->assertEquals(100000, $firstPayment->starting_balance);
        
        $lastPayment = $loan->amortizationSchedule()->orderBy('month_number', 'desc')->first();
        $this->assertEquals(0, $lastPayment->ending_balance);
    }

    public function test_handles_extra_payments()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 200000,
            'annual_interest_rate' => 4.0,
            'loan_term_years' => 30,
            'monthly_extra_payment' => 500
        ]);

        $loan = $this->service->calculateLoan($data);

        $this->assertDatabaseHas('extra_repayment_schedules', [
            'loan_id' => $loan->id,
            'extra_repayment' => 500
        ]);

        $this->assertLessThan(360, $loan->actual_term_months);
        $this->assertGreaterThan(0, $loan->time_saved_months);
        $this->assertGreaterThan(0, $loan->interest_saved);
    }

    public function test_calculates_effective_interest_rate()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 250000,
            'annual_interest_rate' => 5.5,
            'loan_term_years' => 25,
            'monthly_extra_payment' => 300
        ]);

        $loan = $this->service->calculateLoan($data);

        $this->assertGreaterThan($loan->annual_interest_rate, $loan->effective_interest_rate);
        $this->assertGreaterThan(0, $loan->effective_interest_rate);
    }

    public function test_handles_zero_interest_rate()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 120000,
            'annual_interest_rate' => 0,
            'loan_term_years' => 10,
            'monthly_extra_payment' => 0
        ]);

        $loan = $this->service->calculateLoan($data);

        $expectedPayment = 120000 / (10 * 12);
        $this->assertEquals($expectedPayment, $loan->monthly_payment);
    }

    public function test_validates_loan_payoff_with_extra_payments()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 50000,
            'annual_interest_rate' => 3.5,
            'loan_term_years' => 15,
            'monthly_extra_payment' => 1000
        ]);

        $loan = $this->service->calculateLoan($data);

        $lastExtraPayment = $loan->extraRepaymentSchedule()
            ->orderBy('month_number', 'desc')
            ->first();

        $this->assertEquals(0, $lastExtraPayment->ending_balance_after_extra);
        $this->assertEquals(0, $lastExtraPayment->remaining_term_months);
    }

    public function test_calculates_interest_components_correctly()
    {
        $data = LoanDTO::fromArray([
            'loan_amount' => 150000,
            'annual_interest_rate' => 6.0,
            'loan_term_years' => 20,
            'monthly_extra_payment' => 0
        ]);

        $loan = $this->service->calculateLoan($data);

        $firstPayment = $loan->amortizationSchedule()->where('month_number', 1)->first();
        $expectedFirstInterest = 150000 * (6.0 / 12 / 100);
        
        $this->assertEquals(round($expectedFirstInterest, 2), $firstPayment->interest_component);
        
        $calculatedPayment = $firstPayment->principal_component + $firstPayment->interest_component;
        $this->assertEquals($loan->monthly_payment, round($calculatedPayment, 2));
    }
}