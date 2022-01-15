<?php

namespace App\Http\Controllers;

use App\Http\Requests\Twit\DestroyTwitRequest;
use App\Http\Requests\Twit\StoreTwitRequest;
use App\Http\Requests\Twit\TwitRequest;
use App\Http\Requests\Twit\TwitsRequest;
use App\Http\Requests\Twit\UpdateTwitRequest;
use App\Http\Resources\TwitCollection;
use App\Http\Resources\TwitResource;
use App\Models\Twit;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TwitController extends Controller
{
    use ApiResponse;

    /**
     * Twit listesi
     *
     * @param TwitsRequest $request
     * @return JsonResponse
     */
    public function index(TwitsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $twits = Twit::whereTwitterAccount($validated['twitter_account']);

        if ($validated['twitter_account'] != $request->user()->twitter_account)
            $twits = $twits->whereStatus(true);

        $twits = $twits->orderBy('date', 'desc')
            ->paginate(20);

        return $this->success(new TwitCollection($twits));
    }

    /**
     * Yeni twit ekle
     *
     * @param StoreTwitRequest $request
     * @return JsonResponse
     */
    public function store(StoreTwitRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $twit = $request->user()->twits()->create([
            'twit' => $validated['twit'],
            'status' => $validated['status'],
            'date' => now(),
        ]);

        return $this->success(new TwitResource($twit), 201);
    }

    /**
     * Twit görüntüle.
     *
     * @param TwitRequest $request
     * @param Twit $twit
     * @return JsonResponse
     */
    public function show(TwitRequest $request, Twit $twit): JsonResponse
    {
        return $this->success(new TwitResource($twit));
    }

    /**
     * Twit güncelle
     *
     * @param UpdateTwitRequest $request
     * @param Twit $twit
     * @return JsonResponse
     */
    public function update(UpdateTwitRequest $request, Twit $twit): JsonResponse
    {
        $validated = $request->validated();

        $twit->update(
            collect($validated)
                ->only(['status', 'twit'])
                ->toArray()
        );

        return $this->success(new TwitResource($twit));
    }

    /**
     * Twit sil
     *
     * @param DestroyTwitRequest $request
     * @param Twit $twit
     * @return JsonResponse
     */
    public function destroy(DestroyTwitRequest $request, Twit $twit): JsonResponse
    {
        $twit->delete();

        return $this->success(new TwitResource($twit), 202, __('twit.deleted'));
    }
}
