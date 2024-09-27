<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendResetPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $otp;

    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function handle()
    {
        try {
            Mail::to($this->user->email)->send(new ResetPasswordMail($this->user, $this->otp));
        } catch (Exception $e) {
            Log::error('Failed to send reset password email: ' . $e->getMessage());
            throw $e;
        }
    }
}
