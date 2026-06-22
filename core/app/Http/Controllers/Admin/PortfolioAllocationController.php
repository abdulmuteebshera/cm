<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioAllocation;
use Illuminate\Http\Request;

class PortfolioAllocationController extends Controller
{
    public function index()
    {
        $pageTitle   = 'Portfolio Allocation';
        $allocations = PortfolioAllocation::ordered()->paginate(getPaginate());
        $totalActive = PortfolioAllocation::where('status', 1)->sum('percentage');
        return view('admin.portfolio_allocation.index', compact('pageTitle', 'allocations', 'totalActive'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $allocation = new PortfolioAllocation();
        $this->submitData($allocation, $request);

        $notify[] = ['success', 'Allocation added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);

        $allocation = PortfolioAllocation::findOrFail($id);
        $this->submitData($allocation, $request);

        $notify[] = ['success', 'Allocation updated successfully'];
        return back()->withNotify($notify);
    }

    private function submitData(PortfolioAllocation $allocation, Request $request): void
    {
        $allocation->name        = $request->name;
        $allocation->percentage  = $request->percentage;
        $allocation->color       = $request->color ?: '#1989BE';
        $allocation->description = $request->description;
        $allocation->sort_order  = (int) $request->sort_order;
        $allocation->status      = $request->status ? 1 : 0;
        $allocation->save();
    }

    private function validation(Request $request): void
    {
        $this->validate($request, [
            'name'        => 'required|string|max:120',
            'percentage'  => 'required|numeric|min:0|max:100',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer',
            'status'      => 'nullable|in:0,1',
        ]);
    }

    public function status($id)
    {
        return PortfolioAllocation::changeStatus($id);
    }

    public function delete($id)
    {
        $allocation = PortfolioAllocation::findOrFail($id);
        $allocation->delete();

        $notify[] = ['success', 'Allocation deleted successfully'];
        return back()->withNotify($notify);
    }
}
