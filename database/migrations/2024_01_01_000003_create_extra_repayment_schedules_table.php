<?php

use App\Models\Loan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_repayment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Loan::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('month_number');
            $table->decimal('starting_balance', 12, 2);
            $table->decimal('monthly_payment', 10, 2);
            $table->decimal('principal_component', 10, 2);
            $table->decimal('interest_component', 10, 2);
            $table->decimal('extra_repayment', 10, 2);
            $table->decimal('ending_balance_after_extra', 12, 2);
            $table->unsignedInteger('remaining_term_months');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_repayment_schedules');
    }
};