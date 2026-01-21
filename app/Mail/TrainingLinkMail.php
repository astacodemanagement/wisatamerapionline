<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $training;
    public $link;

    public function __construct($training, $link)
    {
        $this->training = $training;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Link Pelatihan Anda')
                    ->view('emails.training_link')
                    ->with([
                        'training_name' => $this->training->training ? $this->training->training->training_name : $this->training->training_name,
                        'link' => $this->link,
                    ]);
    }
}