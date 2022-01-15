<?php

namespace App\Http\Controllers;

use App\Http\Requests\Twit\ImportLastTwentyTwitsRequest;
use App\Http\Resources\TwitResource;
use App\Services\TheMovieDbService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Son 20 twiti içe aktar
     *
     * @throws Exception
     */
    public function importLastTwentyTwits(ImportLastTwentyTwitsRequest $request): JsonResponse
    {
        $importedTweets = (new TheMovieDbService($request->user()))
            ->load()
            ->import();

        return $this->success(
            TwitResource::collection($importedTweets),
            201,
            __('twit.imported')
        );
    }
}
