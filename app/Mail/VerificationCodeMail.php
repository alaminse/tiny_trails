<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $type; // 'phone' বা 'email' হবে

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $code
     * @param string $type
     */
    public function __construct(User $user, string $code, string $type)
    {
        $this->user = $user;
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        // টাইপ অনুযায়ী সাবজেক্ট ডাইনামিকভাবে সেট করুন
        $subject = $this->type === 'phone'
            ? 'Your Phone Verification Code'
            : 'Your Email Verification Code';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'backend.includes.verification-code',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
