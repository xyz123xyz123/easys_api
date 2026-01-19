<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class GenericMail extends Mailable
{
    public function __construct(
        public string $subjectText,
        public string $htmlBody,
        public array $attachmentsData = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText
        );
    }

    public function content(): Content
    {
        return new Content(
            html: $this->htmlBody
        );
    }

    public function attachments(): array
    {
        return collect($this->attachmentsData)->map(function ($file) {
            return Attachment::fromData(
                fn () => base64_decode($file['data']),
                $file['name']
            )->withMime($file['type']);
        })->toArray();
    }
}
