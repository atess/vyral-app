<?php

namespace App\Listeners;

use App\Services\TheMovieDbService;
use Exception;
use Illuminate\Auth\Events\Registered;

class ImportUserTwitsListener
{
    /**
     * Handle the event.
     *
     * @return void
     * @throws Exception
     */
    public function handle(Registered $event)
    {
        (new TheMovieDbService($event->user))
            ->load()
            ->import();
    }
}
