<?php

namespace Tests\Feature\Contract;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contract test guarding the webhook idempotency fix.
 * The provider may deliver the same event more than once (timeouts,
 * retries). The same event id must result in exactly ONE invoice,
 * no matter how many times it arrives.
 */
class PaymentWebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'id'   => 'evt_rollover_001',
            'type' => 'payment.succeeded',
            'data' => [
                'student_id'     => 42,
                'billing_period' => '2026-09',
                'amount'         => 1500000,
            ],
        ];
    }

    public function test_same_event_delivered_twice_creates_one_invoice(): void
    {
        $this->postJson('/api/webhooks/payments', $this->payload())
            ->assertOk()
            ->assertJsonPath('applied', true);

        // Redelivery of the SAME event id (provider retry storm):
        $this->postJson('/api/webhooks/payments', $this->payload())
            ->assertOk()
            ->assertJsonPath('applied', false);

        $this->assertSame(1, Invoice::count());
    }

    public function test_replay_storm_still_creates_one_invoice(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/webhooks/payments', $this->payload())->assertOk();
        }

        $this->assertSame(1, Invoice::count());
    }
}
