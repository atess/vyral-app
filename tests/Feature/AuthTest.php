<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Yeni kullanıcı kaydı testi
     *
     * @return void
     */
    public function test_register()
    {
        $this->postJson(route('auth.register'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'phone' => '5551112233',
            'password' => 'secret',
            'twitter_account' => 'vyralapp',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'twitter_account',
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseCount('twits', 20);

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
        ]);
    }

    /**
     * Kullanıcı girişi testi
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);

        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'twitter_account',
                    'token',
                    'token_type',
                ],
            ]);
    }

    /**
     * Kullanıcı telefon numarası doğrulama testi
     *
     * @return void
     */
    public function test_phone_verify()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
            'phone_verification_code' => '1000',
        ]);

        $this->post(route('auth.phoneVerify'), [
            'phone' => $user->phone,
            'code' => '1000',
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'twitter_account',
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseMissing('users', [
            'phone' => $user->phone,
            'phone_verified_at' => null,
        ]);
    }

    /**
     * Kullanıcı e-posta adresi doğrulama testi
     *
     * @return void
     */
    public function test_email_verify()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
            'email_verification_code' => '1000',
        ]);

        $this->post(route('auth.emailVerify'), [
            'email' => $user->email,
            'code' => '1000',
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'twitter_account',
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }
}
