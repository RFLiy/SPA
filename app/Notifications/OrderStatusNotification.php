<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;

    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        \Illuminate\Support\Facades\Log::info('Sending email to: ' . $notifiable->email . ' | Status: ' . $this->status);
        $config = $this->getStatusConfig($this->status);

        $mail = (new MailMessage)
            ->subject("Pesanan #{$this->order->order_code} — {$config['label']}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line($config['message'])
            ->line("**No. Pesanan:** #{$this->order->order_code}");

        if (in_array($this->status, ['shipped', 'delivery']) && $this->order->shipping_reference) {
            $mail->line("**Kurir / Sopir:** " . ($this->order->courier_name ?? 'Kurir Internal'))
                ->line("**No. Surat Jalan:** {$this->order->shipping_reference}");
        }

        if ($config['action_url'] && $config['action_label']) {
            $mail->action($config['action_label'], $config['action_url']);
        }

        if ($config['closing']) {
            $mail->line($config['closing']);
        }
        $mail->line('---')
            ->line('📩 Agar email notifikasi pesanan tidak masuk folder Spam, tambahkan **' . config('mail.from.address') . '** ke daftar kontak Anda.');

        return $mail->salutation('Salam, ' . config('app.name'));
    }

    protected function getStatusConfig(string $status): array
    {
        $orderUrl    = route('orders.show', $this->order->id);
        $trackingUrl = route('orders.tracking', $this->order->id);
        $homeUrl     = route('welcome');

        $configs = [
            'waiting_payment' => [
                'label'        => 'Menunggu Pembayaran',
                'message'      => 'Pesanan Anda telah dibuat. Segera lakukan pembayaran sebelum batas waktu yang ditentukan agar pesanan dapat diproses.',
                'action_label' => 'Lihat Detail & Bayar',
                'action_url'   => $orderUrl,
                'closing'      => 'Jika Anda tidak merasa melakukan pemesanan ini, abaikan email ini.',
            ],
            'paid' => [
                'label'        => 'Pembayaran Berhasil',
                'message'      => 'Pembayaran Anda telah kami terima. Pesanan Anda akan segera kami proses.',
                'action_label' => 'Lihat Detail Pesanan',
                'action_url'   => $orderUrl,
                'closing'      => null,
            ],
            'processing' => [
                'label'        => 'Sedang Diproses',
                'message'      => 'Pesanan Anda sedang kami kemas dengan hati-hati. Kami akan segera mengirimkannya kepada Anda.',
                'action_label' => 'Lihat Detail Pesanan',
                'action_url'   => $orderUrl,
                'closing'      => null,
            ],
            'shipped' => [
                'label'        => 'Dalam Pengiriman',
                'message'      => 'Pesanan Anda telah keluar dari gudang dan dalam proses pengiriman! Gunakan nomor Surat Jalan di bawah untuk memantau pengiriman paket Anda.',
                'action_label' => 'Lacak Pengiriman',
                'action_url'   => $trackingUrl,
                'closing'      => null,
            ],
            'delivery' => [
                'label'        => 'Sedang Diantar',
                'message'      => 'Pesanan Anda saat ini sedang dalam perjalanan menuju lokasi Anda oleh kurir/sopir kami.',
                'action_label' => 'Lihat Detail Pesanan',
                'action_url'   => $orderUrl,
                'closing'      => null,
            ],
            'completed' => [
                'label'        => 'Pesanan Selesai',
                'message'      => 'Terima kasih! Pesanan Anda telah selesai. Kami sangat senang bisa melayani Anda.',
                'action_label' => 'Belanja Lagi',
                'action_url'   => $homeUrl,
                'closing'      => null,
            ],
            'cancelled' => [
                'label'        => 'Pesanan Dibatalkan',
                'message'      => 'Pesanan Anda telah dibatalkan. Jika Anda sudah melakukan pembayaran, proses refund akan kami lakukan dalam 3-7 hari kerja.',
                'action_label' => 'Lihat Pesanan',
                'action_url'   => $orderUrl,
                'closing'      => 'Maaf atas ketidaknyamanan ini.',
            ],
        ];

        return $configs[$status] ?? [
            'label'        => strtoupper($status),
            'message'      => "Status pesanan Anda telah diperbarui menjadi {$status}.",
            'action_label' => 'Lihat Detail Pesanan',
            'action_url'   => $orderUrl,
            'closing'      => null,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'order_code' => $this->order->order_code,
            'status'     => $this->status,
            'message'    => "Pesanan #{$this->order->order_code} — " . $this->getStatusConfig($this->status)['label'],
        ];
    }
}
