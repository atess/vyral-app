<?php

namespace App\Http\Controllers;

use App\Events\Verified;
use App\Http\Requests\Auth\EmailVerifyRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PhoneVerifyRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Yeni kullanıcı kayıt
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $emailVerificationCode = random_int(1000, 9999);
            $phoneVerificationCode = random_int(1000, 9999);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => bcrypt($validated['password']),
                'twitter_account' => $validated['twitter_account'],
                'email_verification_code' => $emailVerificationCode,
                'phone_verification_code' => $phoneVerificationCode,
            ]);

            Log::info("email_verification_code: $emailVerificationCode");
            Log::info("phone_verification_code: $phoneVerificationCode");

            event(new Registered($user));

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage());
        }

        return $this->authResponse($user, 201, __('auth.account_created'));
    }

    /**
     * E-posta adresi doğrulama
     *
     * @param EmailVerifyRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function emailVerify(EmailVerifyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::whereEmail($validated['email'])
            ->whereEmailVerificationCode($validated['code']);

        if (!$user->exists())
            throw new Exception(__('auth.failed'));

        $user = $user->first();
        $user->markEmailAsVerified();

        event(new Verified($user));

        return $this->authResponse($user, 200, __('auth.email_verified'));
    }

    /**
     * Telefon numarası doğrulama
     *
     * @param PhoneVerifyRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function phoneVerify(PhoneVerifyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::wherePhone($validated['phone'])
            ->wherePhoneVerificationCode($validated['code']);

        if (!$user->exists())
            throw new Exception(__('auth.failed'));

        $user = $user->first();
        $user->markPhoneAsVerified();

        event(new Verified($user));

        return $this->authResponse($user, 200, __('auth.phone_verified'));
    }

    /**
     * Oturum aç
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::whereEmail($validated['email'])->first();

        if (!$user || !Auth::attempt($validated))
            throw new Exception(__('auth.failed'));

        return $this->authResponse($user);
    }

    /**
     * Yeni kayıt, Giriş, E-posta adresi doğrulama, Telefon numarası doğrulama
     * servislerinin response'u oluşturuluyor.
     *
     * @param $user
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    private function authResponse($user, int $code = 200, ?string $message = null): JsonResponse
    {
        // Önceki token kayıtları temizlendi.
        $user->tokens()->delete();

        return $this->success(
            AuthResource::make($user)
                ->setToken(
                    'Bearer',
                    $user->createToken('API Token')->plainTextToken
                ),
            $code,
            $message,
        );
    }
}
