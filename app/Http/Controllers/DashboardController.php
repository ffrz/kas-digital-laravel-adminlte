<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{

    /**
     * Fungsi untuk menerapkan filter account_id ke query transaksi
     *
     * @param Builder $query Query Eloquent yang akan difilter
     * @param mixed $accountId ID akun yang ingin difilter ('all' untuk semua akun)
     * @return Builder Query Eloquent yang sudah difilter berdasarkan account_id
     */
    private function applyAccountIdFilter(Builder $query, $accountId, $col = 'id'): Builder
    {
        if ($accountId == 'all') {
            return $query;
        }

        return $query->where($col, $accountId);
    }

    public function index(Request $request)
    {
        $filter_active = true; // set selalu true karena periode selalu aktif
        $periods = [
            'today' => 'Hari Ini',
            'yesterday' => 'Kemarin',
            'this_week' => 'Minggu Ini',
            'prev_week' => 'Minggu Kemarin',
            'this_month' => 'Bulan Ini',
            'prev_month' => 'Bulan Kemarin',
            'custom' => 'Custom Period',
        ];

        $filter = [
            'account_id' => $request->get('account_id', $request->session()->get('dashboard.filter.account_id', 'all')),
            'period' => $request->get('period', $request->session()->get('dashboard.filter.period', 'today')),
        ];

        $data = [
            'active_user_count' => User::getActiveCount(),
            'active_cash_account_count' => CashAccount::getActiveCount(),
            'total_balance' => CashAccount::getTotalBalance($filter),
            'total_income' => $total_income = CashTransaction::getTotalIncome($filter),
            'total_expense' => $total_expense = CashTransaction::getTotalExpense($filter),
            'cash_balance' => $total_income - $total_expense,
            'recent_transactions' => CashTransaction::getRecentTransactions($filter),
            'top_incomes' => CashTransaction::getTopIncomes($filter),
            'top_expenses' => CashTransaction::getTopExpenses($filter),
            'selected_account_name' => $this->getSelectedAccountName($filter['account_id']),
            'selected_period' => $periods[$filter['period']],
        ];

        $accounts = CashAccount::getActiveAccounts();
        $cashflow_chart_data = CashTransaction::getCashflowData($filter);
        $account_balance_distribution_chart_data = CashAccount::getAccountBalanceDistributionChartData();
        $income_vs_expense_chart_data = CashTransaction::getIncomeVsExpenseChartData($filter);
        $income_by_category_chart_data = CashTransaction::getIncomeByCategoryChartData($filter);
        $expense_by_category_chart_data = CashTransaction::getExpenseByCategoryChartData($filter);

        return view('pages.dashboard.index', compact(
            'data',
            'filter_active',
            'filter',
            'accounts',
            'cashflow_chart_data',
            'account_balance_distribution_chart_data',
            'income_vs_expense_chart_data',
            'income_by_category_chart_data',
            'expense_by_category_chart_data',
        ));
    }

    /**
     * Mendapatkan nama akun kas berdasarkan ID
     */
    private function getSelectedAccountName($accountId)
    {
        if ($accountId == 'all') return 'Semua Kas';
        return CashAccount::find($accountId)->name ?? 'Tidak Diketahui';
    }
}
