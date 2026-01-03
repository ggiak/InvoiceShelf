<?php

namespace App\Http\Controllers\V1\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendInvoiceRequest;
use App\Models\Invoice;

class SendInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('send invoice', $invoice);

        $invoice->send($request->all());

        $emailLog = \App\Models\EmailLog::where('mailable_type', Invoice::class)
            ->where('mailable_id', $invoice->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'public_url' => $emailLog ? route('invoice', ['email_log' => $emailLog->token]) : null,
        ]);
    }
}
