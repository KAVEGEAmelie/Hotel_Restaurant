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

    /**
     * Create a new message instance.
     * En utilisant "public readonly array $data", on déclare la propriété
     * et on lui assigne sa valeur en une seule ligne. C'est propre et moderne.
     */
    public function __construct(
        public readonly array $data
    ) {
        // Le constructeur est maintenant vide car tout est fait dans la déclaration ci-dessus !
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['email'], $this->data['name']),
            replyTo: [ // C'est une bonne pratique d'ajouter l'email de l'expéditeur en "Reply-To"
                new Address($this->data['email'], $this->data['name']),
            ],
            subject: 'Nouveau message : ' . $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Pas besoin de 'with', Laravel rendra automatiquement la propriété publique 'data' accessible à la vue.
        return new Content(
            markdown: 'emails.contact',
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