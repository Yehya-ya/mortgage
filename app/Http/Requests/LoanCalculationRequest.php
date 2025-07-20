<?php

namespace App\Http\Requests;

use App\DTOs\LoanDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoanCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_amount' => [
                'required',
                'numeric',
                'min:1',
                'max:10000000'
            ],
            'annual_interest_rate' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100'
            ],
            'loan_term_years' => [
                'required',
                'integer',
                'min:1',
                'max:50'
            ],
            'monthly_extra_payment' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'loan_amount.required' => 'Loan amount is required.',
            'loan_amount.numeric' => 'Loan amount must be a valid number.',
            'loan_amount.min' => 'Loan amount must be at least $1.',
            'loan_amount.max' => 'Loan amount cannot exceed $10,000,000.',
            
            'annual_interest_rate.required' => 'Annual interest rate is required.',
            'annual_interest_rate.numeric' => 'Interest rate must be a valid number.',
            'annual_interest_rate.min' => 'Interest rate must be at least 0.01%.',
            'annual_interest_rate.max' => 'Interest rate cannot exceed 100%.',
            
            'loan_term_years.required' => 'Loan term is required.',
            'loan_term_years.integer' => 'Loan term must be a whole number of years.',
            'loan_term_years.min' => 'Loan term must be at least 1 year.',
            'loan_term_years.max' => 'Loan term cannot exceed 50 years.',
            
            'monthly_extra_payment.numeric' => 'Extra payment must be a valid number.',
            'monthly_extra_payment.min' => 'Extra payment cannot be negative.',
            'monthly_extra_payment.max' => 'Extra payment cannot exceed $100,000.'
        ];
    }

    public function toDTO(): LoanDTO
    {
        return new LoanDTO(
            loanAmount: $this->float('loan_amount'),
            annualInterestRate: $this->float('annual_interest_rate'),
            loanTermYears: $this->integer('loan_term_years'),
            monthlyExtraPayment: $this->float('monthly_extra_payment', 0)
        );
    }
}