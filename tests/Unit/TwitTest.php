<?php

namespace Tests\Unit;

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
    public function test_oturumu_acik_kullanici_twit_listesi()
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
    public function test_oturumu_kapali_kullanici_twitleri_goremez()
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
    public function test_kullanicinin_son_20_twitini_ice_aktar()
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
    public function test_yeni_twit_ekle()
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
    public function test_kendi_twitini_goruntule()
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
    public function test_farkli_kullanici_aktif_durumdaki_twiti_goruntuler()
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
     * Farklı bir kullanıcının pasif durumdaki twiti görüntülenemez
     *
     * @return void
     * @throws Exception
     */
    public function test_farkli_kullanici_aktif_olmayan_twiti_goruntuleyemez()
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
    public function test_kullanici_kendi_twitini_gunceller()
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
    public function test_kullanici_kendi_twitini_siler()
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
    public function test_farkli_kullanici_kendine_ait_olmayan_twiti_guncelleyemez()
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
    public function test_farkli_kullanici_kendine_ait_olmayan_twiti_silemez()
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
