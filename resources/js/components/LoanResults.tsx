import React from 'react';
import { DollarSign, TrendingDown, Clock, Percent } from 'lucide-react';
import { LoanResult } from '../types/loan';

interface LoanResultsProps {
    result: LoanResult;
}

const LoanResults: React.FC<LoanResultsProps> = ({ result }) => {
    const formatCurrency = (value: number): string => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
        }).format(value);
    };

    const formatPercent = (value: number): string => {
        return `${value.toFixed(4)}%`;
    };

    const formatMonths = (months: number): string => {
        const years = Math.floor(months / 12);
        const remainingMonths = months % 12;
        
        if (years === 0) {
            return `${remainingMonths} month${remainingMonths !== 1 ? 's' : ''}`;
        }
        
        if (remainingMonths === 0) {
            return `${years} year${years !== 1 ? 's' : ''}`;
        }
        
        return `${years} year${years !== 1 ? 's' : ''}, ${remainingMonths} month${remainingMonths !== 1 ? 's' : ''}`;
    };

    return (
        <div className="space-y-6">
            {/* Loan Summary Header */}
            <div className="card">
                <h2 className="text-2xl font-bold text-gray-900 mb-6">Loan Summary</h2>
                
                <div className="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-700 mb-4">Loan Details</h3>
                        <div className="space-y-3">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Loan Amount:</span>
                                <span className="font-semibold">{formatCurrency(result.loan_amount)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Interest Rate:</span>
                                <span className="font-semibold">{formatPercent(result.annual_interest_rate)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Loan Term:</span>
                                <span className="font-semibold">{result.loan_term_years} years</span>
                            </div>
                            {result.monthly_extra_payment > 0 && (
                                <div className="flex justify-between">
                                    <span className="text-gray-600">Extra Payment:</span>
                                    <span className="font-semibold">{formatCurrency(result.monthly_extra_payment)}/month</span>
                                </div>
                            )}
                        </div>
                    </div>
                    
                    <div>
                        <h3 className="text-lg font-semibold text-gray-700 mb-4">Payment Summary</h3>
                        <div className="space-y-3">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Monthly Payment:</span>
                                <span className="font-semibold text-lg">{formatCurrency(result.monthly_payment)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Total Interest:</span>
                                <span className="font-semibold">{formatCurrency(result.total_interest)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Total Payments:</span>
                                <span className="font-semibold">{formatCurrency(result.total_payments)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Effective Rate:</span>
                                <span className="font-semibold">{formatPercent(result.effective_interest_rate)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Key Metrics Cards */}
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="card text-center">
                    <DollarSign className="w-8 h-8 text-green-600 mx-auto mb-2" />
                    <h3 className="text-lg font-semibold text-gray-900">Monthly Payment</h3>
                    <p className="text-2xl font-bold text-green-600">{formatCurrency(result.monthly_payment)}</p>
                </div>

                <div className="card text-center">
                    <Percent className="w-8 h-8 text-blue-600 mx-auto mb-2" />
                    <h3 className="text-lg font-semibold text-gray-900">Effective Rate</h3>
                    <p className="text-2xl font-bold text-blue-600">{formatPercent(result.effective_interest_rate)}</p>
                </div>

                {result.time_saved_months > 0 && (
                    <>
                        <div className="card text-center">
                            <Clock className="w-8 h-8 text-purple-600 mx-auto mb-2" />
                            <h3 className="text-lg font-semibold text-gray-900">Time Saved</h3>
                            <p className="text-2xl font-bold text-purple-600">{formatMonths(result.time_saved_months)}</p>
                        </div>

                        <div className="card text-center">
                            <TrendingDown className="w-8 h-8 text-orange-600 mx-auto mb-2" />
                            <h3 className="text-lg font-semibold text-gray-900">Interest Saved</h3>
                            <p className="text-2xl font-bold text-orange-600">{formatCurrency(result.interest_saved)}</p>
                        </div>
                    </>
                )}
            </div>

            {/* Extra Payment Benefits */}
            {result.monthly_extra_payment > 0 && (
                <div className="card bg-gradient-to-r from-green-50 to-blue-50 border-green-200">
                    <h3 className="text-xl font-bold text-gray-900 mb-4">Extra Payment Benefits</h3>
                    <div className="grid md:grid-cols-3 gap-4">
                        <div className="text-center">
                            <p className="text-sm text-gray-600">Original Term</p>
                            <p className="text-lg font-semibold">{formatMonths(result.original_term_months)}</p>
                        </div>
                        <div className="text-center">
                            <p className="text-sm text-gray-600">New Term</p>
                            <p className="text-lg font-semibold text-green-600">{formatMonths(result.actual_term_months)}</p>
                        </div>
                        <div className="text-center">
                            <p className="text-sm text-gray-600">Total Savings</p>
                            <p className="text-lg font-semibold text-green-600">{formatCurrency(result.interest_saved)}</p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default LoanResults;