export interface LoanData {
    loan_amount: number;
    annual_interest_rate: number;
    loan_term_years: number;
    monthly_extra_payment: number;
}

export interface LoanResult {
    loan_id: number;
    loan_amount: number;
    annual_interest_rate: number;
    loan_term_years: number;
    monthly_extra_payment: number;
    monthly_payment: number;
    total_payments: number;
    total_interest: number;
    effective_interest_rate: number;
    original_term_months: number;
    actual_term_months: number;
    time_saved_months: number;
    interest_saved: number;
}

export interface AmortizationEntry {
    id: number;
    loan_id: number;
    month_number: number;
    starting_balance: number;
    monthly_payment: number;
    principal_component: number;
    interest_component: number;
    ending_balance: number;
    created_at: string;
    updated_at: string;
}

export interface ExtraRepaymentEntry {
    id: number;
    loan_id: number;
    month_number: number;
    starting_balance: number;
    monthly_payment: number;
    principal_component: number;
    interest_component: number;
    extra_repayment: number;
    ending_balance_after_extra: number;
    remaining_term_months: number;
    created_at: string;
    updated_at: string;
}

export interface ApiResponse<T> {
    success: boolean;
    data?: T;
    message?: string;
    error?: string;
}