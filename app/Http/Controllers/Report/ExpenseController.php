<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends BaseController
{
    public function monthlyExpenseDetail(Request $request)
    {
        if (!$request->has('period')) {
            return view('pages.report.expense.detail.form');
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));

        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);

        $q = Expense::with('category')
            ->whereRaw("(date between '$startDate' and '$endDate')");
        $q->orderBy('id', 'asc');

        $items = $q->get();
        $categories = ExpenseCategory::query()->orderBy('name', 'asc')->get();

        return view('pages.report.expense.detail.print', compact('items', 'categories', 'period'));
    }

    public function monthlyExpenseRecap(Request $request)
    {
        if (!$request->has('period')) {
            return view('pages.report.expense.recap.form');
        }

        $period = extract_daterange_from_input($request->get('period'), date('01-m-Y') . ' - ' . date('t-m-Y'));

        $startDate = datetime_from_input($period[0]);
        $endDate = datetime_from_input($period[1]);

        $q = Expense::whereRaw("(date between '$startDate' and '$endDate')")->orderBy('id', 'asc');

        $expenses = $q->get();

        $categories = ExpenseCategory::query()->orderBy('name', 'asc')->get();
        $categoryByIds = [];

        foreach ($categories as $category) {
            $categoryByIds[$category->id] = $category;
            $category->total = 0;
        }

        foreach ($expenses as $expense) {
            $categoryByIds[$expense->category_id]->total += $expense->amount;
        }

        $items = $categoryByIds;

        return view('pages.report.expense.recap.print', compact('items', 'categories', 'period'));
    }
}
