import React, { useState } from 'react';
import { Calculator, AlertCircle } from 'lucide-react';
import { LoanData } from '../types/loan';

interface LoanFormProps {
    onSubmit: (data: LoanData) => void;
    loading: boolean;
    error: string | null;
}

const LoanForm: React.FC<LoanFormProps> = ({ onSubmit, loading, error }) => {
    const [formData, setFormData] = useState<LoanData>({
        loan_amount: 300000,
        annual_interest_rate: 4.5,
        loan_term_years: 30,
        monthly_extra_payment: 0,
    });

    const [validationErrors, setValidationErrors] = useState<Record<string, string>>({});

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: parseFloat(value) || 0,
        }));

        if (validationErrors[name]) {
            setValidationErrors(prev => ({
                ...prev,
                [name]: '',
            }));
        }
    };

    const validateForm = (): boolean => {
        const errors: Record<string, string> = {};

        if (formData.loan_amount <= 0) {
            errors.loan_amount = 'Loan amount must be greater than 0';
        }
        if (formData.loan_amount > 10000000) {
            errors.loan_amount = 'Loan amount cannot exceed $10,000,000';
        }

        if (formData.annual_interest_rate <= 0) {
            errors.annual_interest_rate = 'Interest rate must be greater than 0';
        }
        if (formData.annual_interest_rate > 100) {
            errors.annual_interest_rate = 'Interest rate cannot exceed 100%';
        }

        if (formData.loan_term_years <= 0) {
            errors.loan_term_years = 'Loan term must be greater than 0';
        }
        if (formData.loan_term_years > 50) {
            errors.loan_term_years = 'Loan term cannot exceed 50 years';
        }

        if (formData.monthly_extra_payment < 0) {
            errors.monthly_extra_payment = 'Extra payment cannot be negative';
        }

        setValidationErrors(errors);
        return Object.keys(errors).length === 0;
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (validateForm()) {
            onSubmit(formData);
        }
    };

    return (
        <div className="card">
            <div className="flex items-center mb-6">
                <Calculator className="w-6 h-6 text-blue-600 mr-2" />
                <h2 className="text-2xl font-bold text-gray-900">Loan Details</h2>
            </div>

            {error && (
                <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div className="flex items-center">
                        <AlertCircle className="w-5 h-5 text-red-600 mr-2" />
                        <p className="text-red-700">{error}</p>
                    </div>
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Loan Amount */}
                <div>
                    <label htmlFor="loan_amount" className="block text-sm font-medium text-gray-700 mb-2">
                        Loan Amount
                    </label>
                    <div className="relative">
                        <span className="absolute left-3 top-2 text-gray-500">$</span>
                        <input
                            type="number"
                            id="loan_amount"
                            name="loan_amount"
                            value={formData.loan_amount}
                            onChange={handleInputChange}
                            className={`input-field pl-8 ${validationErrors.loan_amount ? 'border-red-500' : ''}`}
                            placeholder="300,000"
                        />
                    </div>
                    {validationErrors.loan_amount && (
                        <p className="mt-1 text-sm text-red-600">{validationErrors.loan_amount}</p>
                    )}
                </div>

                {/* Annual Interest Rate */}
                <div>
                    <label htmlFor="annual_interest_rate" className="block text-sm font-medium text-gray-700 mb-2">
                        Annual Interest Rate
                    </label>
                    <div className="relative">
                        <input
                            type="number"
                            id="annual_interest_rate"
                            name="annual_interest_rate"
                            value={formData.annual_interest_rate}
                            onChange={handleInputChange}
                            className={`input-field pr-8 ${validationErrors.annual_interest_rate ? 'border-red-500' : ''}`}
                            placeholder="4.5"
                            step="0.01"
                        />
                        <span className="absolute right-3 top-2 text-gray-500">%</span>
                    </div>
                    {validationErrors.annual_interest_rate && (
                        <p className="mt-1 text-sm text-red-600">{validationErrors.annual_interest_rate}</p>
                    )}
                </div>

                {/* Loan Term */}
                <div>
                    <label htmlFor="loan_term_years" className="block text-sm font-medium text-gray-700 mb-2">
                        Loan Term (Years)
                    </label>
                    <input
                        type="number"
                        id="loan_term_years"
                        name="loan_term_years"
                        value={formData.loan_term_years}
                        onChange={handleInputChange}
                        className={`input-field ${validationErrors.loan_term_years ? 'border-red-500' : ''}`}
                        placeholder="30"
                        step="1"
                    />
                    {validationErrors.loan_term_years && (
                        <p className="mt-1 text-sm text-red-600">{validationErrors.loan_term_years}</p>
                    )}
                </div>

                {/* Monthly Extra Payment */}
                <div>
                    <label htmlFor="monthly_extra_payment" className="block text-sm font-medium text-gray-700 mb-2">
                        Monthly Extra Payment (Optional)
                    </label>
                    <div className="relative">
                        <span className="absolute left-3 top-2 text-gray-500">$</span>
                        <input
                            type="number"
                            id="monthly_extra_payment"
                            name="monthly_extra_payment"
                            value={formData.monthly_extra_payment}
                            onChange={handleInputChange}
                            className={`input-field pl-8 ${validationErrors.monthly_extra_payment ? 'border-red-500' : ''}`}
                            placeholder="0"
                        />
                    </div>
                    {validationErrors.monthly_extra_payment && (
                        <p className="mt-1 text-sm text-red-600">{validationErrors.monthly_extra_payment}</p>
                    )}
                </div>

                {/* Submit Button */}
                <button
                    type="submit"
                    disabled={loading}
                    className={`w-full btn-primary ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
                >
                    {loading ? (
                        <div className="flex items-center justify-center">
                            <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                            Calculating...
                        </div>
                    ) : (
                        <div className="flex items-center justify-center">
                            <Calculator className="w-5 h-5 mr-2" />
                            Calculate Loan
                        </div>
                    )}
                </button>
            </form>
        </div>
    );
};

export default LoanForm;