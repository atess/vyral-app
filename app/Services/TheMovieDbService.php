<?php

namespace App\Services;

use App\Models\Twit;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheMovieDbService
{
    protected $endpoint;
    protected $token;
    protected $user;
    protected $response;

    /**
     * @throws Exception
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->endpoint = config('services.themoviedb.endpoint');
        $this->token = config('services.themoviedb.token');
    }

    /**
     * @throws Exception
     */
    public function load(): TheMovieDbService
    {
        $this->response = Http::acceptJson()
            ->withToken($this->token)
            ->get($this->endpoint, [
                'twitter_account' => $this->user->twitter_account, // zaman kısıtı nedeniyle api olarak the movie db kullanıldı.
                'page' => random_int(1, 500),
                'query' => 'a',
            ]);;

        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function import()
    {
        if (isset($this->response['results']))
            try {
                DB::beginTransaction();

                if (count($this->response['results']) > 0) {
                    $rows = [];

                    collect($this->response['results'])
                        ->map(function ($movie) use (&$rows) {
                            $rows[] = new Twit([
                                'twit' => !empty($movie['overview']) ? $movie['overview'] : $movie['original_title'],
                                'date' => !empty($movie['release_date']) ? $movie['release_date'] : now(),
                            ]);
                        });

                    $this->user->twits()->saveMany($rows);
                }

                DB::commit();
            } catch (Exception $exception) {
                DB::rollback();

                throw new Exception($exception->getMessage());
            }
        else
            throw new Exception('service error');

        return $this->user->twits;
    }
}
