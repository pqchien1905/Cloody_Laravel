<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Notification - Thông báo đặt lại mật khẩu qua email
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Token đặt lại mật khẩu.
     *
     * @var string
     */
    public $token;

    /**
     * Tạo một instance notification mới.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Lấy các kênh gửi thông báo.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Lấy biểu diễn email của thông báo.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu - Cloody')
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }

    /**
     * Lấy URL đặt lại mật khẩu cho người dùng được thông báo.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable): string
    {
        return URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], true);
    }

    /**
     * Lấy biểu diễn dạng mảng của thông báo.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
