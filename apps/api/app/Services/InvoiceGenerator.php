<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

/**
 * Generates tuition invoices.
 *
 * Idempotency: an invoice is uniquely keyed by (student_id, billing_period)
 * — enforced by a unique index and by updateOrCreate here — so retries,
 * webhook redeliveries and queue replays can never double-charge a parent.
 * This is the structural fix for the term-rollover double-charge incident.
 */
class InvoiceGenerator
{
    public function generateForPayment(int $studentId, string $period, int $amount): Invoice
    {
        return DB::transaction(fn () => Invoice::updateOrCreate(
            ['student_id' => $studentId, 'billing_period' => $period],
            ['amount' => $amount, 'status' => 'paid'],
        ));
    }
}
