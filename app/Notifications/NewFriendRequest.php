<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewFriendRequest extends Notification implements ShouldQueue
{
    use Queueable;
    //pravimo property $user, on stize iz add_friend() metoda FriendshipsControllera kad neko nekog doda za prijatelja,
    //to je user koji je dodao nekog za prijatelja
    public $user;

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
        return ['mail','broadcast','database'];
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
                    //kreiramo mail za usera koji je dodat za prijatelja i salje mo mu ime usera koji ga je dodao
                    ->line('You recived a new friend request from ' . $this->user->name)
                    //saljemo mu link ka profilu usera koji ga je dodao
                    ->action('View profile', route('profile', ['slug' => $this->user->slug]))
                    ->line('Thank you for using our socnetwork!');
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
          'name' => $this->user->name,
          'message' => ' send you a friend request.'
        ];
    }


}
