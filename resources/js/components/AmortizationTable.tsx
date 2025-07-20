import React, { useState, useEffect } from 'react';
import { Table, Download, ToggleLeft, ToggleRight } from 'lucide-react';

interface AmortizationEntry {
    month_number: number;
    starting_balance: number;
    monthly_payment: number;
    principal_component: number;
    interest_component: number;
    ending_balance: number;
    extra_repayment?: number;
    ending_balance_after_extra?: number;
    remaining_term_months?: number;
}

interface LoanDetails {
    loan_amount: number;
    annual_interest_rate: number;
    loan_term_years: number;
    monthly_payment: number;
    monthly_extra_payment?: number;
    effective_interest_rate: number;
    time_saved_months?: number;
    interest_saved?: number;
}

interface AmortizationTableProps {
    loanId: number;
}

const AmortizationTable: React.FC<AmortizationTableProps> = ({ loanId }) => {
    const [standardSchedule, setStandardSchedule] = useState<AmortizationEntry[]>([]);
    const [extraSchedule, setExtraSchedule] = useState<AmortizationEntry[]>([]);
    const [loanDetails, setLoanDetails] = useState<LoanDetails | null>(null);
    const [showExtraSchedule, setShowExtraSchedule] = useState(false);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetchSchedules();
    }, [loanId]);

    const fetchSchedules = async () => {
        setLoading(true);
        setError(null);

        try {
            const standardResponse = await fetch(`/api/loans/${loanId}/amortization-schedule`);
            const standardData = await standardResponse.json();

            if (standardResponse.ok) {
                setStandardSchedule(standardData.data.schedule);
                setLoanDetails(standardData.data.loan_details);
            }

            const extraResponse = await fetch(`/api/loans/${loanId}/extra-payment-schedule`);
            if (extraResponse.ok) {
                const extraData = await extraResponse.json();
                setExtraSchedule(extraData.data.schedule);
                setLoanDetails(extraData.data.loan_details);
            }
        } catch (err) {
            setError('Failed to load amortization schedules');
        } finally {
            setLoading(false);
        }
    };

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

    const exportToCSV = () => {
        const schedule = showExtraSchedule ? extraSchedule : standardSchedule;
        const headers = showExtraSchedule 
            ? ['Month', 'Starting Balance', 'Payment', 'Principal', 'Interest', 'Extra Payment', 'Ending Balance', 'Remaining Term']
            : ['Month', 'Starting Balance', 'Payment', 'Principal', 'Interest', 'Ending Balance'];

        const csvContent = [
            headers.join(','),
            ...schedule.map(entry => {
                const row = [
                    entry.month_number,
                    entry.starting_balance,
                    entry.monthly_payment,
                    entry.principal_component,
                    entry.interest_component,
                ];

                if (showExtraSchedule) {
                    row.push(
                        entry.extra_repayment || 0,
                        entry.ending_balance_after_extra || 0,
                        entry.remaining_term_months || 0
                    );
                } else {
                    row.push(entry.ending_balance);
                }

                return row.join(',');
            })
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `amortization-schedule-${showExtraSchedule ? 'extra-payments' : 'standard'}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    };

    if (loading) {
        return (
            <div className="card">
                <div className="animate-pulse">
                    <div className="h-8 bg-gray-200 rounded mb-4"></div>
                    <div className="space-y-3">
                        {[...Array(5)].map((_, i) => (
                            <div key={i} className="h-4 bg-gray-200 rounded"></div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="card">
                <div className="text-center py-8">
                    <p className="text-red-600">{error}</p>
                </div>
            </div>
        );
    }

    const currentSchedule = showExtraSchedule ? extraSchedule : standardSchedule;
    const hasExtraSchedule = extraSchedule.length > 0;

    return (
        <div className="space-y-6">
            {/* Schedule Header */}
            <div className="card">
                <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                        <Table className="w-6 h-6 text-blue-600 mr-2" />
                        <h2 className="text-2xl font-bold text-gray-900">
                            {showExtraSchedule ? 'Extra Payment Schedule' : 'Amortization Schedule'}
                        </h2>
                    </div>
                    <div className="flex items-center space-x-4">
                        {hasExtraSchedule && (
                            <button
                                onClick={() => setShowExtraSchedule(!showExtraSchedule)}
                                className="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                            >
                                {showExtraSchedule ? (
                                    <ToggleRight className="w-5 h-5 text-green-600" />
                                ) : (
                                    <ToggleLeft className="w-5 h-5 text-gray-400" />
                                )}
                                <span className="text-sm font-medium">
                                    {showExtraSchedule ? 'Extra Payments' : 'Standard'}
                                </span>
                            </button>
                        )}
                        <button
                            onClick={exportToCSV}
                            className="flex items-center space-x-2 btn-secondary"
                        >
                            <Download className="w-4 h-4" />
                            <span>Export CSV</span>
                        </button>
                    </div>
                </div>

                {/* Loan Details Header */}
                {loanDetails && (
                    <div className="bg-gray-50 rounded-lg p-4 mb-6">
                        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span className="text-gray-600">Loan Amount:</span>
                                <div className="font-semibold">{formatCurrency(loanDetails.loan_amount)}</div>
                            </div>
                            <div>
                                <span className="text-gray-600">Interest Rate:</span>
                                <div className="font-semibold">{formatPercent(loanDetails.annual_interest_rate)}</div>
                            </div>
                            <div>
                                <span className="text-gray-600">Term:</span>
                                <div className="font-semibold">{loanDetails.loan_term_years} years</div>
                            </div>
                            <div>
                                <span className="text-gray-600">Monthly Payment:</span>
                                <div className="font-semibold">{formatCurrency(loanDetails.monthly_payment)}</div>
                            </div>
                            {showExtraSchedule && loanDetails.monthly_extra_payment && (
                                <>
                                    <div>
                                        <span className="text-gray-600">Extra Payment:</span>
                                        <div className="font-semibold">{formatCurrency(loanDetails.monthly_extra_payment)}</div>
                                    </div>
                                    <div>
                                        <span className="text-gray-600">Time Saved:</span>
                                        <div className="font-semibold text-green-600">
                                            {loanDetails.time_saved_months} months
                                        </div>
                                    </div>
                                    <div>
                                        <span className="text-gray-600">Interest Saved:</span>
                                        <div className="font-semibold text-green-600">
                                            {formatCurrency(loanDetails.interest_saved || 0)}
                                        </div>
                                    </div>
                                </>
                            )}
                            <div>
                                <span className="text-gray-600">Effective Rate:</span>
                                <div className="font-semibold">{formatPercent(loanDetails.effective_interest_rate)}</div>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* Amortization Table */}
            <div className="card">
                <div className="overflow-x-auto custom-scrollbar">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="table-header">Month</th>
                                <th className="table-header">Starting Balance</th>
                                <th className="table-header">Payment</th>
                                <th className="table-header">Principal</th>
                                <th className="table-header">Interest</th>
                                {showExtraSchedule && (
                                    <>
                                        <th className="table-header">Extra Payment</th>
                                        <th className="table-header">Balance After Extra</th>
                                        <th className="table-header">Remaining Term</th>
                                    </>
                                )}
                                {!showExtraSchedule && (
                                    <th className="table-header">Ending Balance</th>
                                )}
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {currentSchedule.map((entry, index) => (
                                <tr key={entry.month_number} className={index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                    <td className="table-cell font-medium">{entry.month_number}</td>
                                    <td className="table-cell">{formatCurrency(entry.starting_balance)}</td>
                                    <td className="table-cell">{formatCurrency(entry.monthly_payment)}</td>
                                    <td className="table-cell">{formatCurrency(entry.principal_component)}</td>
                                    <td className="table-cell">{formatCurrency(entry.interest_component)}</td>
                                    {showExtraSchedule && (
                                        <>
                                            <td className="table-cell">
                                                {entry.extra_repayment ? formatCurrency(entry.extra_repayment) : '-'}
                                            </td>
                                            <td className="table-cell">
                                                {formatCurrency(entry.ending_balance_after_extra || 0)}
                                            </td>
                                            <td className="table-cell">
                                                {entry.remaining_term_months || 0} months
                                            </td>
                                        </>
                                    )}
                                    {!showExtraSchedule && (
                                        <td className="table-cell">{formatCurrency(entry.ending_balance)}</td>
                                    )}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {currentSchedule.length === 0 && (
                    <div className="text-center py-8">
                        <p className="text-gray-500">No schedule data available</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default AmortizationTable;