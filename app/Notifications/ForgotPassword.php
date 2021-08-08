<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ForgotPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
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

        // $url = url('/password/reset/'.$this->user->password_reset_token);

        return (new MailMessage)
            ->subject('Forgot Password OTP. - '.config('app.name'))
            ->greeting('Hello '.$notifiable->first_name)
            ->line('OTP : '.$notifiable->password_reset_token)
            // ->markdown('vendor.mail.forgotpassword.index', ['password_reset_token' => $notifiable->password_reset_token,'firstname' => $notifiable->first_name])
            // ->greeting('Hello, '.$this->user->first_name)
            // ->action('Reset Password', $url)
            ->line('Thank you for using our application!');

            // return Redirect::back()->withErrors(['msg', 'Mail not sent']);
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
