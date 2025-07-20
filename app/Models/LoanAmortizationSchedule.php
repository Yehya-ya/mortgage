<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAmortizationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'month_number',
        'starting_balance',
        'monthly_payment',
        'principal_component',
        'interest_component',
        'ending_balance'
    ];

    protected $casts = [
        'starting_balance' => 'float',
        'monthly_payment' => 'float',
        'principal_component' => 'float',
        'interest_component' => 'float',
        'ending_balance' => 'float',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
