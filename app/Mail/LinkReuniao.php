<?php

namespace App\Mail;

use App\Models\Treinamento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LinkReuniao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Treinamento $treinamento,
        public string $url,
        public string $nome,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Link da reunião · '.$this->treinamento->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.link-reuniao');
    }
}
