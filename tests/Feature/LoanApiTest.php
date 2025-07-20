<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_loan_with_valid_data()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 30,
            'monthly_extra_payment' => 200
        ];

        $response = $this->postJson('/api/loans/calculate', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'loan_id',
                    'loan_amount',
                    'annual_interest_rate',
                    'loan_term_years',
                    'monthly_extra_payment',
                    'monthly_payment',
                    'total_payments',
                    'total_interest',
                    'effective_interest_rate',
                    'original_term_months',
                    'actual_term_months',
                    'time_saved_months',
                    'interest_saved'
                ]
            ]);

        $this->assertDatabaseHas('loans', [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 30,
            'monthly_extra_payment' => 200,
            'monthly_payment' => 1520.06,
            'effective_interest_rate' => 5.6455,
            'original_term_months' => 360,
            "time_saved_months" => 76,
        ]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/loans/calculate', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'loan_amount',
                'annual_interest_rate',
                'loan_term_years'
            ]);
    }

    public function test_validates_negative_loan_amount()
    {
        $data = [
            'loan_amount' => -50000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 30
        ];

        $response = $this->postJson('/api/loans/calculate', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['loan_amount']);
    }

    public function test_validates_invalid_interest_rate()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => -1,
            'loan_term_years' => 30
        ];

        $response = $this->postJson('/api/loans/calculate', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['annual_interest_rate']);
    }

    public function test_validates_invalid_loan_term()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 0
        ];

        $response = $this->postJson('/api/loans/calculate', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['loan_term_years']);
    }

    public function test_can_retrieve_amortization_schedule()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 5,
        ];

        $loan = $this->postJson('/api/loans/calculate', $data)->json()['data'];

        $response = $this->getJson("/api/loans/" . $loan['loan_id'] . "/amortization-schedule");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'loan_details' => [
                        'loan_amount',
                        'annual_interest_rate',
                        'loan_term_years',
                        'monthly_payment',
                        'effective_interest_rate'
                    ],
                    'schedule' => [
                        '*' => [
                            'month_number',
                            'starting_balance',
                            'monthly_payment',
                            'principal_component',
                            'interest_component',
                            'ending_balance'
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_retrieve_extra_payment_schedule()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 5,
            'monthly_extra_payment' => 1000,
        ];

        $loan = $this->postJson('/api/loans/calculate', $data)->json()['data'];


        $response = $this->getJson("/api/loans/" . $loan['loan_id'] . "/extra-payment-schedule");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'loan_details' => [
                        'loan_amount',
                        'annual_interest_rate',
                        'loan_term_years',
                        'monthly_extra_payment',
                        'monthly_payment',
                        'effective_interest_rate',
                        'time_saved_months',
                        'interest_saved'
                    ],
                    'schedule' => [
                        '*' => [
                            'month_number',
                            'starting_balance',
                            'monthly_payment',
                            'principal_component',
                            'interest_component',
                            'extra_repayment',
                            'ending_balance_after_extra',
                            'remaining_term_months'
                        ]
                    ]
                ]
            ]);
    }

    public function test_returns_404_for_extra_payment_schedule_without_extra_payments()
    {
        $data = [
            'loan_amount' => 300000,
            'annual_interest_rate' => 4.5,
            'loan_term_years' => 5
        ];

        $loan = $this->postJson('/api/loans/calculate', $data)->json()['data'];

        $response = $this->getJson("/api/loans/" . $loan['loan_id'] . "/extra-payment-schedule");

        $response->assertStatus(404);
    }
}