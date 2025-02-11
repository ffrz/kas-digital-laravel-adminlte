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


    /**
     * Fungsi untuk menerapkan filter periode ke query transaksi
     *
     * @param Builder $query Query Eloquent yang akan difilter
     * @param string|null $period Periode yang ingin difilter (today, yesterday, this_week, prev_week, this_month, prev_month)
     * @return Builder Query Eloquent yang sudah difilter
     */
    private function applyPeriodFilter(Builder $query, ?string $period): Builder
    {
        if (!$period || $period === 'all') {
            return $query; // Jika periodenya 'all' atau null, langsung return query tanpa filter tambahan
        }

        switch ($period) {
            case 'today':
                $query->whereDate('date', Carbon::today());
                break;

            case 'yesterday':
                $query->whereDate('date', Carbon::yesterday());
                break;

            case 'this_week':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;

            case 'prev_week':
                $query->whereBetween('date', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
                break;

            case 'this_month':
                $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;

            case 'prev_month':
                $query->whereBetween('date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ]);
                break;
        }

        return $query;
    }

    public function getActiveUserCount()
    {
        return User::where('is_active', 1)->count();
    }

    public function getTotalBalance($filter)
    {
        $q = CashAccount::query();
        $q->where('active', 1);
        $q = $this->applyAccountIdFilter($q, $filter['account_id']);
        return $q->sum('balance');
    }

    public function getActiveAccountCount()
    {
        return CashAccount::where('active', 1)->count();
    }

    public function getTotalIncome($filter)
    {
        $q = CashTransaction::where('amount', '>', 0);
        $q = $this->applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = $this->applyPeriodFilter($q, $filter['period']);
        return $q->sum('amount');
    }

    public function getRecentTransactions($filter, $count = 5)
    {
        $q = CashTransaction::with(['account']);
        $q = $this->applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        return $q->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($count)
            ->get();
    }

    public function getTopIncomes($filter, $count = 5)
    {
        $q = CashTransaction::query();
        $q = $this->applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = $this->applyPeriodFilter($q, $filter['period']);
        $q->where('amount', '>', 0);
        return $q->orderBy('amount', 'desc')
            ->limit($count)
            ->get();
    }

    public function getTopExpenses($filter, $count = 5)
    {
        $q = CashTransaction::query();
        $q = $this->applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = $this->applyPeriodFilter($q, $filter['period']);
        $q->where('amount', '<', 0);
        return $q->orderBy('amount', 'asc')
            ->limit($count)
            ->get();
    }

    public function getTotalExpense($filter)
    {
        $q = CashTransaction::where('amount', '<', 0);
        $q = $this->applyAccountIdFilter($q, $filter['account_id'], 'account_id');
        $q = $this->applyPeriodFilter($q, $filter['period']);
        return abs($q->sum('amount'));
    }

    public function index(Request $request)
    {
        $filter_active = false;
        $periods = [
            'today' => 'Hari Ini',
            'yesterday' => 'Kemarin',
            'this_week' => 'Minggu Ini',
            'prev_week' => 'Minggu Kemarin',
            'this_month' => 'Bulan Ini',
            'prev_month' => 'Bulan Kemarin',
            'custom' => 'Custom Period',
        ];
        $accounts = CashAccount::all()->keyBy('id');

        $filter = [
            'account_id' => $request->get('account_id', $request->session()->get('dashboard.filter.account_id', 'all')),
            'period' => $request->get('period', $request->session()->get('dashboard.filter.period', 'today')),
        ];

        $data = [
            'active_user_count' => $this->getActiveUserCount(),
            'active_cash_account_count' => $this->getActiveAccountCount(),
            'total_balance' => $this->getTotalBalance($filter),
            'total_income' => $total_income = $this->getTotalIncome($filter),
            'total_expense' => $total_expense = $this->getTotalExpense($filter),
            'cash_balance' => $total_income - $total_expense,
            'recent_transactions' => $this->getRecentTransactions($filter),
            'top_incomes' => $this->getTopIncomes($filter),
            'top_expenses' => $this->getTopExpenses($filter),
            'selected_account_name' => $filter['account_id'] == 'all' ? 'Semua Kas' : $accounts[$filter['account_id']]->name,
            'selected_period' => $periods[$filter['period']],
        ];

        $accounts = CashAccount::where('active', '1')->orderBy('name', 'asc')->get();

        $cashflow_chart_data = CashTransaction::getCashflowData($filter);
        return view('pages.dashboard.index', compact('data', 'filter_active', 'filter', 'accounts', 'cashflow_chart_data'));
    }
}
