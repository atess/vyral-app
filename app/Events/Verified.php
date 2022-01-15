<?php

namespace App\Events;

use App\Contracts\MustVerifyPhone;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Verified
{
    use SerializesModels, Dispatchable;

    /**
     * The verified user.
     *
     * @var MustVerifyEmail|MustVerifyPhone
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param MustVerifyEmail|MustVerifyPhone $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
