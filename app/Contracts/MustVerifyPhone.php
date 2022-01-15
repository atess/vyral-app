<?php

namespace App\Contracts;

interface MustVerifyPhone
{
    /**
     * Determine if the user has verified their phone.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool;

    /**
     * Mark the given user's phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool;

    /**
     * Send the phone verification notification.
     *
     * @return void
     */
    public function sendPhoneVerificationNotification();

    /**
     * Get the phone that should be used for verification.
     *
     * @return string
     */
    public function getPhoneForVerification(): string;
}
