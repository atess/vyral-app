<?php

namespace App\Models;

use App\Traits\MustVerifyPhone;
use Exception;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create(array $array)
 * @method static whereEmail(mixed $email)
 * @method static wherePhone(mixed $email)
 * @property string $phone_verified_at
 * @property string $email_verified_at
 * @property string $phone
 * @property string $twitter_account
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmail, MustVerifyPhone;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'twitter_account',
        'password',
        'phone_verified_at',
        'email_verification_code',
        'phone_verification_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Kullanıcının twitleri
     *
     * @return HasMany
     */
    public function twits(): HasMany
    {
        return $this->hasMany(Twit::class, 'twitter_account', 'twitter_account')
            ->orderBy('created_at', 'desc')
            ->limit(20);
    }

    /**
     * Twitler yerine random 20 film kaydı alındı ve response'daki
     * film açıklaması alanı kullanıcının twit'i olarak kaydedildi.
     *
     * @throws Exception
     */
    public function importLastTwentyTwits()
    {

    }
}
