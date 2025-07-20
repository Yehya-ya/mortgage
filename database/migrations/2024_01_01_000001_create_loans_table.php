<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('annual_interest_rate', 5, 4);
            $table->unsignedInteger('loan_term_years');
            $table->decimal('monthly_extra_payment', 10, 2)->default(0);
            $table->decimal('monthly_payment', 10, 2);
            $table->decimal('total_payments', 12, 2)->nullable();
            $table->decimal('total_interest', 12, 2)->nullable();
            $table->decimal('effective_interest_rate', 5, 4)->nullable();
            $table->unsignedInteger('original_term_months');
            $table->unsignedInteger('actual_term_months')->nullable();
            $table->unsignedInteger('time_saved_months')->default(0);
            $table->decimal('interest_saved', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};