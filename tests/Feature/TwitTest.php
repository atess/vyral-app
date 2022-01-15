<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TheMovieDbService;
use Exception;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TwitTest extends TestCase
{
    /**
     * Twit listesi testi
     *
     * @return void
     * @throws Exception
     */
    public function test_get_twits()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);

        Sanctum::actingAs($user);

        (new TheMovieDbService($user))->load()->import();

        $url = route('twit.index') . '?' . http_build_query(['twitter_account' => 'vyralapp']);

        $this->getJson($url)
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'list' => [
                        '*' => [
                            'id',
                            'twit',
                            'date',
                            'status',
                        ]
                    ],
                    'pagination' => [
                        'total',
                        'count',
                        'per_page',
                        'current_page',
                        'total_pages',
                    ]
                ]
            ]);
    }

    /**
     * Oturumu kapalı olan kullanıcılar twitleri göremez
     *
     * @return void
     * @throws Exception
     */
    public function test_unauthorized_get_twits()
    {
        $url = route('twit.index') . '?' . http_build_query(['twitter_account' => 'vyralapp']);

        $this->getJson($url)
            ->assertStatus(401);
    }

    /**
     * Kullanıcının son 20 twitini getir
     *
     * @return void
     * @throws Exception
     */
    public function test_get_last_twenty_twits()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);

        Sanctum::actingAs($user);

        (new TheMovieDbService($user))->load()->import();

        $this->assertDatabaseCount('twits', 20);
    }

    /**
     * Twit ekle
     *
     * @return void
     * @throws Exception
     */
    public function test_create_twit()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);

        Sanctum::actingAs($user);

        $twit = Str::random(50);

        $this->postJson(route('twit.store'), [
            'twit' => $twit,
            'status' => false,
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'twit',
                    'date',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('twits', [
            'twit' => $twit,
        ]);
    }

    /**
     * Kendine ait bir twiti görüntüle
     *
     * @return void
     * @throws Exception
     */
    public function test_show_my_twit()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        Sanctum::actingAs($user);

        $twitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $this->getJson(route('twit.show', ['twit' => $twit->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'twit',
                    'date',
                    'status',
                ],
            ]);
    }

    /**
     * Farklı bir kullanıcının aktif durumdaki twitini görüntüle
     *
     * @return void
     * @throws Exception
     */
    public function test_different_users_show_active_tweet()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        $twitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => true,
            'date' => now(),
        ]);

        $user2 = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5552112233',
            'twitter_account' => 'xyralapp',
        ]);
        Sanctum::actingAs($user2);

        $this->getJson(route("twit.show", ['twit' => $twit->id]))
            ->assertStatus(200);
    }

    /**
     * Farklı bir kullanıcının aktif durumdaki twiti görüntülenemez
     *
     * @return void
     * @throws Exception
     */
    public function test_different_users_not_show_passive_tweet()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        $twitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $user2 = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5552112233',
            'twitter_account' => 'xyralapp',
        ]);
        Sanctum::actingAs($user2);

        $this->getJson(route("twit.show", ['twit' => $twit->id]))
            ->assertStatus(403);
    }

    /**
     * Twit güncelle
     *
     * @return void
     * @throws Exception
     */
    public function test_update_twit()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        Sanctum::actingAs($user);

        $twitText = Str::random(50);
        $updatedTwitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $this->putJson(route("twit.update", ['twit' => $twit->id]), [
            'twit' => $updatedTwitText,
            'status' => true,
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'twit',
                    'date',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('twits', [
            'id' => $twit->id,
            'twit' => $updatedTwitText,
            'status' => true,
        ]);
    }

    /**
     * Twit sil
     *
     * @return void
     * @throws Exception
     */
    public function test_delete_twit()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        Sanctum::actingAs($user);

        $twitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $this->deleteJson(route("twit.destroy", ['twit' => $twit->id]))
            ->assertStatus(202)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'twit',
                    'date',
                    'status',
                ],
            ]);

        $this->assertDatabaseMissing('twits', [
            'id' => $twit->id,
        ]);
    }

    /**
     * Kullanıcı kendine ait olmayan bir twiti güncelleyemez
     *
     * @return void
     * @throws Exception
     */
    public function test_different_user_cannot_update_my_tweet()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        $twitText = Str::random(50);
        $updatedTwitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $user2 = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5552112233',
            'twitter_account' => 'xyralapp',
        ]);
        Sanctum::actingAs($user2);

        $this->putJson(route("twit.update", ['twit' => $twit->id]), [
            'twit' => $updatedTwitText,
            'status' => true,
        ])->assertStatus(403);

        $this->assertDatabaseMissing('twits', [
            'id' => $twit->id,
            'twit' => $updatedTwitText,
            'status' => true,
        ]);
    }

    /**
     * Kullanıcı kendine ait olmayan bir twiti silemez
     *
     * @return void
     * @throws Exception
     */
    public function test_different_user_cannot_delete_my_tweet()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5551112233',
            'twitter_account' => 'vyralapp',
        ]);
        $twitText = Str::random(50);

        $twit = $user->twits()->create([
            'twit' => $twitText,
            'status' => false,
            'date' => now(),
        ]);

        $user2 = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => '5552112233',
            'twitter_account' => 'xyralapp',
        ]);
        Sanctum::actingAs($user2);

        $this->deleteJson(route("twit.destroy", ['twit' => $twit->id]))
            ->assertStatus(403);

        $this->assertDatabaseHas('twits', [
            'id' => $twit->id,
        ]);
    }
}
