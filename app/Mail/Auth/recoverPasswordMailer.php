<?php

namespace App\Mail\auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class recoverPasswordMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $appName;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($user,$password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->appName = config('app.name');
        $this->verificationUrl = config('app.urlFront').'/'.$password; //agregar ruta aqui, pero se debe enviar con el remember_token
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->appName.', recuperación de contraseña',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.password',
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

    public function build()
    {
        return $this->subject($this->appName.', recuperación de contraseña')
                    ->view('mails.password')
                    ->with([
                        'user' => $this->user,
                        'appName' => $this->appName,
                        'verificationUrl' => $this->verificationUrl,
                    ]);
    }
}
