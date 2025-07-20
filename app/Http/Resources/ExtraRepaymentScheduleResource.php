<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraRepaymentScheduleResource extends JsonResource
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
            'extra_repayment' => $this->extra_repayment,
            'ending_balance_after_extra' => $this->ending_balance_after_extra,
            'remaining_term_months' => $this->remaining_term_months,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
