<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Mail;

trait SendOtpMail
{
    /**
     * Send OTP email
     *
     * @param string $email
     * @param string $name
     * @param string|int $otp
     * @return bool
     */
    public function sendOtpMail(string $email, string $name, $otp): bool
    {
        try {
            $data = [
                'name' => $name,
                'otp'  => $otp,
            ];

            Mail::send('emails.otp', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Easy Logics - Your app login code');
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('OTP Mail Error: ' . $e->getMessage());
            return false;
        }
    }
}
