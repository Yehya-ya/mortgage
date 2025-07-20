<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'loan_amount' => 'float',
        'annual_interest_rate' => 'float',
        'monthly_extra_payment' => 'float',
        'monthly_payment' => 'float',
        'total_payments' => 'float',
        'total_interest' => 'float',
        'effective_interest_rate' => 'float',
        'interest_saved' => 'float'
    ];

    public function amortizationSchedule(): HasMany
    {
        return $this->hasMany(LoanAmortizationSchedule::class);
    }
}