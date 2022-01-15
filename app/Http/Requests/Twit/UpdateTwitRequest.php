<?php

namespace App\Http\Requests\Twit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTwitRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'twit' => 'required|max:1000',
            'status' => 'required|boolean',
        ];
    }
}
