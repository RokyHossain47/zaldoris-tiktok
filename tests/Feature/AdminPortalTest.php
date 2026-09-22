<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_via_admin_portal(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@zaldoris.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_dashboard_loads_metrics_and_users(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200)
            ->assertSee('Dashboard Overview')
            ->assertSee('Total Users')
            ->assertSee('User Management List');
    }

    public function test_admin_can_toggle_user_block_and_coins(): void
    {
        $admin = User::where('role', 'admin')->first();
        $buyer = User::where('role', 'buyer')->first();

        // 1. Toggle Block
        $this->actingAs($admin)->post("/admin/users/{$buyer->id}/toggle-block");
        $this->assertTrue($buyer->fresh()->is_suspended);

        // 2. Adjust Coins
        $this->actingAs($admin)->post("/admin/users/{$buyer->id}/update-coins", [
            'coin_balance' => 9999,
        ]);
        $this->assertEquals(9999, $buyer->fresh()->coin_balance);
    }
}
