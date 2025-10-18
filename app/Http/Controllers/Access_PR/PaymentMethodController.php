<?php

namespace App\Http\Controllers\Access_PR;

use App\Http\Controllers\Controller;
use App\Models\Access_PR\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('system_access:payment_method');
    }

    public function index(Request $request)
    {
        $query = PaymentMethod::query();
        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $methods = $query->orderBy('name')->paginate(15);
        return view('Access_PR.payment_methods.index', compact('methods'));
    }

    public function create()
    {
        return view('Access_PR.payment_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_methods,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            PaymentMethod::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('payment-methods.index')->with('success', 'Payment method created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create: '.$e->getMessage());
        }
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('Access_PR.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_methods,name,'.$paymentMethod->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $paymentMethod->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('payment-methods.index')->with('success', 'Payment method updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update: '.$e->getMessage());
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();
            return response()->json(['success' => true, 'message' => 'Payment method deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->update(['is_active' => !$paymentMethod->is_active, 'updated_by' => Auth::id()]);
            return response()->json(['success' => true, 'is_active' => $paymentMethod->is_active]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
