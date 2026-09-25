<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseSalesRecord;
use Illuminate\Http\Request;

class PurchaseSalesRecordController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $recordsQuery = PurchaseSalesRecord::query();

        if ($month) {
            $recordsQuery->whereMonth('purchase_date', '=', date('m', strtotime($month)))
                ->whereYear('purchase_date', '=', date('Y', strtotime($month)));
        }

        $records = $recordsQuery->orderByDesc('purchase_date')->get();

        $monthlyTotal = $records->sum('purchase_price');

        return view('admin.kopen.index', compact('records', 'month', 'monthlyTotal'));
    }

    public function create()
    {
        return view('admin.kopen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date|after_or_equal:purchase_date',
            'sale_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        PurchaseSalesRecord::create($validated);

        return redirect()->route('admin.purchase-sales.index')->with('success', 'Record added successfully.');
    }

    public function edit($id)
    {
        $record = PurchaseSalesRecord::findOrFail($id);

        return view('admin.kopen.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date|after_or_equal:purchase_date',
            'sale_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $record = PurchaseSalesRecord::findOrFail($id);
        $record->update($validated);

        return redirect()->route('admin.purchase-sales.index')->with('success', 'Record bijgewerkt.');
    }

    public function destroy($id)
    {
        $record = PurchaseSalesRecord::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.purchase-sales.index')->with('success', 'Record verwijderd.');
    }

    public function reken()
    {
        return view('admin.rekenen.btw-calculator');
    }
}
