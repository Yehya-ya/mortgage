<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'loan_id' => $this->id,
            'loan_amount' => $this->loan_amount,
            'annual_interest_rate' => $this->annual_interest_rate,
            'loan_term_years' => $this->loan_term_years,
            'monthly_extra_payment' => $this->monthly_extra_payment,
            'monthly_payment' => $this->monthly_payment,
            'total_payments' => $this->total_payments,
            'total_interest' => $this->total_interest,
            'effective_interest_rate' => $this->effective_interest_rate,
            'original_term_months' => $this->original_term_months,
            'actual_term_months' => $this->actual_term_months,
            'time_saved_months' => $this->time_saved_months,
            'interest_saved' => $this->interest_saved
        ];
    }
}
