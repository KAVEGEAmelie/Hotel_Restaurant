<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly array $data
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['email'], $this->data['name']),
            replyTo: [new Address($this->data['email'], $this->data['name'])],
            subject: 'Nouveau Message : ' . $this->data['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.form',
        );
    }
}
