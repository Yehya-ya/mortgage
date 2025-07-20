<?php

namespace App\DTOs;

final readonly class LoanDTO
{
    public function __construct(
        public float $loanAmount,
        public float $annualInterestRate,
        public int $loanTermYears,
        public ?float $monthlyExtraPayment = 0
    )
    {
        //
    }

    public function toArray(): array
    {
        return [
            'loan_amount' => $this->loanAmount,
            'annual_interest_rate' => $this->annualInterestRate,
            'loan_term_years' => $this->loanTermYears,
            'monthly_extra_payment' => $this->monthlyExtraPayment
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            loanAmount: $data['loan_amount'],
            annualInterestRate: $data['annual_interest_rate'],
            loanTermYears: $data['loan_term_years'],
            monthlyExtraPayment: $data['monthly_extra_payment'] ?? 0
        );
    }
}
