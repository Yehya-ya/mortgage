<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraRepaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'month_number',
        'starting_balance',
        'monthly_payment',
        'principal_component',
        'interest_component',
        'extra_repayment',
        'ending_balance_after_extra',
        'remaining_term_months'
    ];

    protected $casts = [
        'starting_balance' => 'float',
        'monthly_payment' => 'float',
        'principal_component' => 'float',
        'interest_component' => 'float',
        'extra_repayment' => 'float',
        'ending_balance_after_extra' => 'float',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}