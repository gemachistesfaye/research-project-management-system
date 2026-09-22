<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        RateLimiter::clear('login.127.0.0.1');
        $this->seed(\Database\Seeders\RbacSeeder::class);
    }

    // ─── Public Password Reset DISABLED ─────────────────────────────────────
    // Self-service /forgot-password is removed because Staff ID + Email are
    // NOT private secrets (printed on ID cards). Password resets go through
    // the System Administrator only (SDD SCR-16, Table 29).

    public function test_public_forgot_password_route_is_disabled()
    {
        // The route must NOT be accessible publicly — must return 404
        $response = $this->get('/forgot-password');
        $response->assertStatus(404);
    }

    // ─── Admin-only Password Reset ───────────────────────────────────────────

    public function test_admin_can_reset_user_password_via_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Services\RbacService::syncUserRole($admin);
        $user  = User::factory()->create([
            'email'    => 'target@gmu.edu.et',
            'password' => Hash::make('OldPass1!'),
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.users.reset-password', $user->id),
            ['new_password' => 'NewAdmin@9']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewAdmin@9', $user->fresh()->password));
    }

    public function test_admin_reset_rejects_weak_password()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Services\RbacService::syncUserRole($admin);
        $user  = User::factory()->create([
            'password' => Hash::make('OldPass1!'),
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.users.reset-password', $user->id),
            ['new_password' => 'simple']   // no uppercase / number / symbol
        );

        $response->assertSessionHasErrors('new_password');
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    public function test_unauthenticated_user_cannot_access_admin_reset()
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass1!')]);

        // Without login, trying to hit admin reset must redirect to login
        $response = $this->post(route('admin.users.reset-password', $user->id), [
            'new_password' => 'NewAdmin@9',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    // ─── Brute-force Lockout Tests ───────────────────────────────────────────

    public function test_login_locked_after_five_failed_attempts()
    {
        $user = User::factory()->create([
            'email'    => 'lockme@gmu.edu.et',
            'password' => Hash::make('RealPass1!'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => 'lockme@gmu.edu.et',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt — even correct password — must be blocked
        $response = $this->post('/login', [
            'email'    => 'lockme@gmu.edu.et',
            'password' => 'RealPass1!',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Too many failed login attempts', $errors[0]);
    }

    public function test_successful_login_clears_lockout_counter()
    {
        $user = User::factory()->create([
            'email'    => 'gooduser@gmu.edu.et',
            'password' => Hash::make('GoodPass1@'),
        ]);

        // 3 wrong attempts (below lockout)
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'email'    => 'gooduser@gmu.edu.et',
                'password' => 'wrongpassword',
            ]);
        }

        // Correct login — must succeed and clear counter
        $response = $this->post('/login', [
            'email'    => 'gooduser@gmu.edu.et',
            'password' => 'GoodPass1@',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_change_password_from_profile_with_valid_current_password()
    {
        $user = User::factory()->create([
            'email'    => 'profileuser@gmu.edu.et',
            'password' => Hash::make('Current@123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.change-password'), [
            'current_password'          => 'Current@123',
            'new_password'              => 'BrandNew@456',
            'new_password_confirmation' => 'BrandNew@456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('BrandNew@456', $user->fresh()->password));
    }

    public function test_profile_change_password_rejects_wrong_current_password()
    {
        $user = User::factory()->create([
            'email'    => 'profileuser2@gmu.edu.et',
            'password' => Hash::make('Current@123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.change-password'), [
            'current_password'          => 'WrongPassword@123',
            'new_password'              => 'BrandNew@456',
            'new_password_confirmation' => 'BrandNew@456',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Current@123', $user->fresh()->password));
    }
}
