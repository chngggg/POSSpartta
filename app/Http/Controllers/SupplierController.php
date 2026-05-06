<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(10);
        return view('stock.supplier-index', compact('suppliers'));
    }

    public function create()
    {
        return view('stock.supplier-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        Supplier::create($validated);

        return redirect()->route('stock.supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        return view('stock.supplier-edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('stock.supplier.index')
            ->with('success', 'Supplier berhasil diupdate!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('stock.supplier.index')
            ->with('success', 'Supplier berhasil dihapus!');
    }
}
