import React, { useState } from 'react';
import { Calculator, DollarSign, Percent, Calendar, PlusCircle } from 'lucide-react';
import LoanForm from '../components/LoanForm';
import LoanResults from '../components/LoanResults';
import AmortizationTable from '../components/AmortizationTable';
import { LoanData, LoanResult } from '../types/loan';

const MortgageCalculator: React.FC<{}> = () => {
  const [loanResult, setLoanResult] = useState<LoanResult | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleLoanCalculation = async (loanData: LoanData) => {
      setLoading(true);
      setError(null);

      try {
          const response = await fetch('/api/loans/calculate', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              },
              body: JSON.stringify(loanData),
          });

          const data = await response.json();

          if (!response.ok) {
              throw new Error(data.message || 'Failed to calculate loan');
          }

          setLoanResult(data.data);
      } catch (err) {
          setError(err instanceof Error ? err.message : 'An error occurred');
      } finally {
          setLoading(false);
      }
  };

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Header */}
      <div className="mb-8 text-center">
        <div className="mb-4 flex items-center justify-center">
          <Calculator className="mr-3 h-12 w-12 text-blue-600" />
          <h1 className="text-4xl font-bold text-gray-900">Mortgage Calculator</h1>
        </div>
        <p className="mx-auto max-w-2xl text-xl text-gray-600">
          Calculate your monthly payments, view amortization schedules, and see how extra payments can save you money
        </p>
      </div>

      {/* Features Grid */}
      <div className="mb-8 grid gap-6 md:grid-cols-4">
        <div className="p-4 text-center">
          <DollarSign className="mx-auto mb-2 h-8 w-8 text-green-600" />
          <h3 className="font-semibold text-gray-900">Monthly Payments</h3>
          <p className="text-sm text-gray-600">Calculate exact monthly payment amounts</p>
        </div>
        <div className="p-4 text-center">
          <Percent className="mx-auto mb-2 h-8 w-8 text-blue-600" />
          <h3 className="font-semibold text-gray-900">Interest Rates</h3>
          <p className="text-sm text-gray-600">See effective rates with extra payments</p>
        </div>
        <div className="p-4 text-center">
          <Calendar className="mx-auto mb-2 h-8 w-8 text-purple-600" />
          <h3 className="font-semibold text-gray-900">Amortization</h3>
          <p className="text-sm text-gray-600">Detailed payment breakdown schedules</p>
        </div>
        <div className="p-4 text-center">
          <PlusCircle className="mx-auto mb-2 h-8 w-8 text-orange-600" />
          <h3 className="font-semibold text-gray-900">Extra Payments</h3>
          <p className="text-sm text-gray-600">See savings from additional payments</p>
        </div>
      </div>

      {/* Main Content */}
      <div className="grid gap-8 lg:grid-cols-3">
        {/* Loan Form */}
        <div className="lg:col-span-1">
          <LoanForm onSubmit={handleLoanCalculation} loading={loading} error={error} />
        </div>

        {/* Results */}
        <div className="lg:col-span-2">
          {loanResult ? (
            <div className="space-y-6">
              <LoanResults result={loanResult} />
              <AmortizationTable loanId={loanResult.loan_id} />
            </div>
          ) : (
            <div className="card py-12 text-center">
              <Calculator className="mx-auto mb-4 h-16 w-16 text-gray-300" />
              <h3 className="mb-2 text-xl font-semibold text-gray-500">Ready to Calculate</h3>
              <p className="text-gray-400">Enter your loan details to see monthly payments and amortization schedules</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default MortgageCalculator;
