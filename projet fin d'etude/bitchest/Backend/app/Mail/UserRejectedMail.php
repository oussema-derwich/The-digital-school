<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $rejectionMessage;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $rejectionMessage
     */
    public function __construct($email, $rejectionMessage)
    {
        $this->email = $email;
        $this->rejectionMessage = $rejectionMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Bitchest Account Has Been Rejected')
            ->view('emails.user_rejected') // Vue de l'e-mail
            ->with([
                'email' => $this->email,
                'rejectionMessage' => $this->rejectionMessage,
            ]);
    }
}
