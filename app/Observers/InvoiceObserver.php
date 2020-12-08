<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    public function created(Invoice $invoice)
    {
        $invoice->reference = 'FACT-'.str_pad($invoice->id, 5, '0', STR_PAD_LEFT);
        $invoice->save();
    }
}
