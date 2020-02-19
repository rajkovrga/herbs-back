<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    private string $desc;

    private string $title;

    private string $email;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $desc
     * @param string $email
     */
    public function __construct($title, $desc, $email)
    {
        $this->title = $title;
        $this->desc = $desc;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from($this->email)
                    ->view('contact',[
                        'desc' => $this->desc
                    ])
                    ->subject($this->title);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
