<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmFinanceEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index()
    {
        $pageTitle = 'Finance Ledger';
        $entries   = CrmFinanceEntry::with('creator')->orderByDesc('entry_date')->paginate(getPaginate());
        $income    = (float) CrmFinanceEntry::where('type', 'income')->sum('amount');
        $expense   = (float) CrmFinanceEntry::where('type', 'expense')->sum('amount');
        $summary   = [
            'income'  => $income,
            'expense' => $expense,
            'net'     => $income - $expense,
        ];

        return view('crm.finance.index', compact('pageTitle', 'entries', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'type'       => 'required|in:income,expense,asset,liability,equity',
            'category'   => 'nullable|string|max:80',
            'title'      => 'required|string|max:160',
            'amount'     => 'required|numeric',
            'currency'   => 'nullable|string|max:10',
            'notes'      => 'nullable|string',
        ]);

        CrmFinanceEntry::create([
            'entry_date' => $request->entry_date,
            'type'       => $request->type,
            'category'   => $request->category,
            'title'      => $request->title,
            'amount'     => $request->amount,
            'currency'   => $request->currency ?: 'USD',
            'notes'      => $request->notes,
            'created_by' => Auth::guard('crm')->id(),
        ]);

        $notify[] = ['success', 'Finance entry saved'];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        CrmFinanceEntry::findOrFail($id)->delete();
        $notify[] = ['success', 'Entry deleted'];
        return back()->withNotify($notify);
    }
}
