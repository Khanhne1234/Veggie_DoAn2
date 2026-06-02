<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // public function toMail(object $notifiable): MailMessage
    // {
    //     $resetUrl = URL::route('password.reset', [
    //         'token' => $this->token,
    //         'email' => $notifiable->email,
    //     ]);

    //     return (new MailMessage)
    //         ->subject('Đặt lại mật khẩu cho tài khoản Veggie')
    //         ->greeting('Xin chào ' . $notifiable->name . ',')
    //         ->line('Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản Veggie của bạn.')
    //         ->line('Nếu bạn không yêu cầu thao tác này, vui lòng bỏ qua email này.')
    //         ->action('Đặt lại mật khẩu', $resetUrl)
    //         ->line('Liên kết đặt lại mật khẩu sẽ hết hạn sau ' . config('auth.passwords.users.expire') . ' phút.')
    //         ->salutation('Trân trọng, Đội ngũ Veggie');
    // }
}
