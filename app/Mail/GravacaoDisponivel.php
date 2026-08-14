<?php

namespace App\Mail;

use App\Models\Gravacao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GravacaoDisponivel extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Gravacao $gravacao,
        public string $url,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gravação disponível · '.$this->gravacao->tituloOrigem(),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.gravacao-disponivel');
    }
}
