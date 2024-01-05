<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Promotion;



class AddPromo extends Notification
{
    protected $promotion;

    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
        public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Salut ! ' . $notifiable->name . ',')
            ->line('Une nouvelle promotion a été ajoutée.')
            ->action('Voir la promotion', url('/promotions'))
            ->line('Merci de votre utilisation de notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
         return [
            'promotion_id' => $this->promotion->id,
            'product_id' => $this->promotion->product_id,
            'product_name' => $this->promotion->product->name, // Assuming the product has a 'name' attribute
            'promotion_title' => $this->promotion->title,
        ];
    }
}
