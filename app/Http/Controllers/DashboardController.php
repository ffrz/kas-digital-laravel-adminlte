<?php
/**
 * Hak Cipta (C) 2025 Fahmi Fauzi Rahman
 * Seluruh Hak Cipta Dilindungi Undang-Undang.
 *
 * File ini bersifat rahasia dan merupakan hak milik eksklusif.
 * Dilarang keras menjual kembali aplikasi ini kepada pihak lain
 * dalam bentuk apapun.
 *
 * Penulis  : Fahmi Fauzi Rahman
 * Kontak   : fahmifauzirahman@gmail.com
 * Youtube  : https://www.youtube.com/@hobi_coding
 * Facebook : https://www.facebook.com/fahmifauzirahman
 * Instagram: https://www.instagram.com/fahmi.fauzi.rahman
 * Tiktok   : https://www.tiktok.com/@ffr__85
 * Lisensi  : Hak Milik (Proprietary)
 */

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
    public function index(Request $request)
    {
        $periods = [
            'today' => 'Hari Ini',
            'yesterday' => 'Kemarin',
            'this_week' => 'Minggu Ini',
            'prev_week' => 'Minggu Kemarin',
            'this_month' => 'Bulan Ini',
            'prev_month' => 'Bulan Kemarin',
            'custom' => 'Custom Period',
        ];

        $is_reset = $request->get('action');
        $filter = [
            'account_id' => $is_reset ? 'all' : $request->get('account_id', 'all'),
            'period' => $is_reset ? 'this_month' : $request->get('period', 'this_month'),
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
