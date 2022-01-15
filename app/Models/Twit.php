<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static whereTwitterAccount(mixed $twitter_account)
 */
class Twit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'twit',
        'twitter_account',
        'status',
        'date',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'twitter_account', 'twitter_account');
    }
}
