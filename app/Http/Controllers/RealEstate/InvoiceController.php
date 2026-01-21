<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Get invoices where user is payer or user is student
        $invoices = Invoice::where('payer_user_id', $user->id)
            ->orWhere('student_user_id', $user->id)
            ->with(['tenant', 'student', 'payer'])
            ->orderBy('due_date', 'desc')
            ->paginate(10);

        return view('real-estate.invoices.index', compact('invoices', 'user'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, string $id): View
    {
        $user = $request->user();
        
        $invoice = Invoice::where(function ($query) use ($user) {
                $query->where('payer_user_id', $user->id)
                      ->orWhere('student_user_id', $user->id);
            })
            ->where('id', $id)
            ->with(['tenant', 'student', 'payer'])
            ->firstOrFail();

        return view('real-estate.invoices.show', compact('invoice', 'user'));
    }

    /**
     * Process payment for the specified invoice.
     */
    public function pay(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        
        $invoice = Invoice::where(function ($query) use ($user) {
                $query->where('payer_user_id', $user->id)
                      ->orWhere('student_user_id', $user->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        // TODO: Implement actual payment processing logic
        // For now, just mark as paid
        $invoice->update(['status' => 'paid']);

        return redirect()->route('invoices.show', $id)
            ->with('status', 'invoice-paid');
    }

    /**
     * Process payment for all pending invoices.
     */
    public function payAll(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Get all pending invoices
        $pendingInvoices = Invoice::where(function ($query) use ($user) {
                $query->where('payer_user_id', $user->id)
                      ->orWhere('student_user_id', $user->id);
            })
            ->where('status', 'pending')
            ->get();

        // TODO: Implement actual bulk payment processing logic
        // For now, just mark all as paid
        foreach ($pendingInvoices as $invoice) {
            $invoice->update(['status' => 'paid']);
        }

        return redirect()->route('invoices.index')
            ->with('status', 'all-invoices-paid');
    }
}

