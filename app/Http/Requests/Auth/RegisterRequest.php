<?php

namespace App\Http\Requests\Auth;

use App\Rules\TwitterAccountRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10|max:10',
            'password' => 'required',
            'twitter_account' => ['required', new TwitterAccountRule],
        ];
    }
}
