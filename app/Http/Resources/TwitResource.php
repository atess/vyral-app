<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $twitter_account
 * @property mixed $status
 * @property mixed $id
 * @property mixed $twit
 * @property mixed $date
 */
class TwitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return array(
            'id' => $this->id,
            'twit' => $this->twit,
            'date' => $this->date,
            'twitter_account' => $this->when($request->user()->twitter_account != $this->twitter_account, $this->twitter_account),
            'status' => $this->when($request->user()->twitter_account == $this->twitter_account, $this->status),
        );
    }
}
