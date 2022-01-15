<?php

namespace App\Http\Requests\Twit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property mixed $twitter_account
 */
class DestroyTwitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $twit = $this->route('twit');
        return Auth::check() && $this->user()->twitter_account == $twit->twitter_account;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
