<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyText;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body)
    {
        $this->subjectText = $subject;
        $this->bodyText = $body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS', 'admin@example.com'),
            subject: $this->subjectText,
            replyTo: env('MAIL_FROM_ADDRESS', 'admin@example.com')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
