<?php

namespace App\Listeners;

use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Mail\TwoFactorCodeMail;

class SendTwoFactorCodeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TwoFactorAuthenticationChallenged $event): void
    {
        $cacheKey = '2fa_code_' . $event->user->id;
        
        // Check if we already have a code for this user (avoid double-send)
        if (Cache::has($cacheKey)) {
            Log::info('2FA Listener: Code already exists for user ' . $event->user->id . ', skipping email.');
            return; // Don't send another email
        }

        // Generate a random 6 digit code
        $code = rand(100000, 999999);
        
        // Cache the code for 10 minutes
        Cache::put($cacheKey, $code, 600);
        Log::info('2FA Listener: Generated NEW code for user ' . $event->user->id . ': ' . $code);

        // Send the email ONLY for new codes
        try {
            Mail::to($event->user->email)->send(new TwoFactorCodeMail($code));
            Log::info('2FA Listener: Email sent to ' . $event->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send 2FA code: ' . $e->getMessage());
        }
    }
}
