<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashTransactionCategory;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function addCashTransactionCategory(Request $request)
    {
        $category = new CashTransactionCategory($request->all());
        $category->save();
        return response()->json([
            'status' => 'success',
            'data' => $category,
            'message' => 'Kategori baru telah ditambahkan.'
        ], 200);
    }

}
