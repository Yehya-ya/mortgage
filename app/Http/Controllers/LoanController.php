<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanCalculationRequest;
use App\Http\Resources\ExtraRepaymentScheduleResource;
use App\Http\Resources\LoanAmortizationScheduleResource;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Services\MortgageCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    private MortgageCalculatorService $calculatorService;

    public function __construct(MortgageCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function calculate(LoanCalculationRequest $request): JsonResponse
    {
        $loan = $this->calculatorService->calculateLoan($request->toDTO());

        return response()->json([
            'data' => new LoanResource($loan)
        ], Response::HTTP_CREATED);
    }

    public function getAmortizationSchedule(Loan $loan): JsonResponse
    {
        $schedule = $loan->amortizationSchedule()
            ->orderBy('month_number')
            ->get();

        return response()->json([
            'data' => [
                'loan_details' => new LoanResource($loan),
                'schedule' => LoanAmortizationScheduleResource::collection($schedule),
            ]
        ]);
    }

    public function getExtraPaymentSchedule(Loan $loan): JsonResponse
    {
        $schedule = $loan->extraRepaymentSchedule()
            ->orderBy('month_number')
            ->get();

        if ($schedule->isEmpty()) {
            return response()->json([
                'message' => 'No extra payment schedule found for this loan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
                'loan_details' => new LoanResource($loan),
                'schedule' => ExtraRepaymentScheduleResource::collection($schedule),
            ]
        ]);
    }
}
