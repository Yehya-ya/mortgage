<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanAmortizationScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'month_number' => $this->month_number,
            'starting_balance' => $this->starting_balance,
            'monthly_payment' => $this->monthly_payment,
            'principal_component' => $this->principal_component,
            'interest_component' => $this->interest_component,
            'ending_balance' => $this->ending_balance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
