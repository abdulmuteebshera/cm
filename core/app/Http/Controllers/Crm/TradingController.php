<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmFundPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingController extends Controller
{
    public function index()
    {
        $pageTitle = 'Company Fund Positions';
        $positions = CrmFundPosition::with('manager')->orderByDesc('id')->paginate(getPaginate());
        $summary   = [
            'allocated' => (float) CrmFundPosition::sum('allocated_amount'),
            'market'    => (float) CrmFundPosition::sum('market_value'),
            'pnl'       => (float) CrmFundPosition::sum('pnl'),
        ];

        return view('crm.trading.index', compact('pageTitle', 'positions', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:160',
            'asset_class'      => 'nullable|string|max:80',
            'venue'            => 'nullable|string|max:120',
            'symbol'           => 'nullable|string|max:80',
            'currency'         => 'nullable|string|max:10',
            'allocated_amount' => 'required|numeric|min:0',
            'market_value'     => 'nullable|numeric',
            'pnl'              => 'nullable|numeric',
            'pnl_percent'      => 'nullable|numeric',
            'status'           => 'required|in:open,closed,pending',
            'opened_on'        => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        CrmFundPosition::create([
            'name'             => $request->name,
            'asset_class'      => $request->asset_class,
            'venue'            => $request->venue,
            'symbol'           => $request->symbol,
            'currency'         => $request->currency ?: 'USD',
            'allocated_amount' => $request->allocated_amount,
            'market_value'     => $request->market_value ?: $request->allocated_amount,
            'pnl'              => $request->pnl ?: 0,
            'pnl_percent'      => $request->pnl_percent ?: 0,
            'status'           => $request->status,
            'opened_on'        => $request->opened_on,
            'notes'            => $request->notes,
            'managed_by'       => Auth::guard('crm')->id(),
        ]);

        $notify[] = ['success', 'Fund position recorded'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $position = CrmFundPosition::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:160',
            'asset_class'      => 'nullable|string|max:80',
            'venue'            => 'nullable|string|max:120',
            'symbol'           => 'nullable|string|max:80',
            'currency'         => 'nullable|string|max:10',
            'allocated_amount' => 'required|numeric|min:0',
            'market_value'     => 'nullable|numeric',
            'pnl'              => 'nullable|numeric',
            'pnl_percent'      => 'nullable|numeric',
            'status'           => 'required|in:open,closed,pending',
            'opened_on'        => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $position->fill($request->only([
            'name', 'asset_class', 'venue', 'symbol', 'currency',
            'allocated_amount', 'market_value', 'pnl', 'pnl_percent',
            'status', 'opened_on', 'notes',
        ]));
        $position->save();

        $notify[] = ['success', 'Position updated'];
        return back()->withNotify($notify);
    }
}
