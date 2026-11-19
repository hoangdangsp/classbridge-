<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvoiceGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Payment webhook receiver.
 *
 * Idempotency contract (written after the rollover double-charge incident):
 *  - Every provider event carries a unique event id. The first delivery is
 *    recorded in `processed_webhook_events`; any redelivery of the same id
 *    is acknowledged with 200 and applied ZERO times.
 *  - Invoice creation is additionally guarded by the generator's own
 *    idempotency key (student_id + billing_period), so even two DIFFERENT
 *    events about the same charge cannot mint two invoices.
 */
class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, InvoiceGenerator $invoices): JsonResponse
    {
        $eventId = (string) $request->input('id');
        $type    = (string) $request->input('type');

        $applied = DB::transaction(function () use ($eventId, $type, $request, $invoices) {
            // Unique index on event_id makes this insert the lock:
            // a duplicate delivery throws and we treat it as already-applied.
            try {
                DB::table('processed_webhook_events')->insert([
                    'event_id'    => $eventId,
                    'type'        => $type,
                    'received_at' => now(),
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                return false; // redelivery: ack, do not re-apply
            }

            if ($type === 'payment.succeeded') {
                $invoices->generateForPayment(
                    studentId: (int) $request->input('data.student_id'),
                    period:    (string) $request->input('data.billing_period'),
                    amount:    (int) $request->input('data.amount'),
                );
            }

            return true;
        });

        return response()->json(['received' => true, 'applied' => $applied]);
    }
}
