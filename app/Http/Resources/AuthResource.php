<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $twitter_account
 * @property string $token
 * @property string $token_type
 */
class AuthResource extends JsonResource
{

    /**
     * @param string $type
     * @param string $token
     * @return $this
     */
    public function setToken(string $type, string $token): AuthResource
    {
        $this->token_type = $type;
        $this->token = $token;
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'twitter_account' => $this->twitter_account,
            'token' => $this->token,
            'token_type' => $this->token_type,
        ];
    }
}
