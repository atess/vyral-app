<?php

namespace App\Http\Requests\Twit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $twitter_account
 * @property boolean $status
 */
class TwitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $twit = $this->route('twit');

        return Auth::check() &&
            ($this->user()->twitter_account == $twit->twitter_account || $twit->status == true);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
