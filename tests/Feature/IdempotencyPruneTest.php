<?php

namespace Tests\Feature;

use App\Models\IdempotencyKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdempotencyPruneTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_old_idempotency_keys_are_pruned(): void
    {
        $user = User::factory()->create();

        // Key yang dibuat lebih dari 24 jam yang lalu (misal 25 jam)
        $oldKey = IdempotencyKey::forceCreate([
            'user_id' => $user->id,
            'key' => 'old-key',
            'request_hash' => 'hash1',
            'response_status' => 200,
            'response_body' => json_encode(['status' => 'ok']),
            'created_at' => now()->subHours(25),
            'updated_at' => now()->subHours(25),
        ]);

        // Key baru yang dibuat kurang dari 24 jam yang lalu (misal 2 jam)
        $recentKey = IdempotencyKey::forceCreate([
            'user_id' => $user->id,
            'key' => 'recent-key',
            'request_hash' => 'hash2',
            'response_status' => 200,
            'response_body' => json_encode(['status' => 'ok']),
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        $this->artisan('model:prune', ['--model' => [IdempotencyKey::class]])->assertSuccessful();

        $this->assertDatabaseMissing('idempotency_keys', ['id' => $oldKey->id]);
        $this->assertDatabaseHas('idempotency_keys', ['id' => $recentKey->id]);
    }
}
