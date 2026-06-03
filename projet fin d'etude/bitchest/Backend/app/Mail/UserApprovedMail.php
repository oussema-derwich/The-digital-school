<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $password
     * @param string $loginUrl
     */
    public function __construct($email, $password, $loginUrl)
    {
        $this->email = $email;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Bitchest Account Has Been Approved')
            ->view('emails.user_approved')
            ->with([
                'email' => $this->email,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
