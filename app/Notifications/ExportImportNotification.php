namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification;

class ExportImportNotification extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
        ]);
    }
}

// Ambil semua notifikasi pengguna saat ini
$notifications = auth()->user()->notifications;

foreach ($notifications as $notification) {
    Notification::make()
        ->title('Notifikasi')
        ->body($notification->data['message']) // Menampilkan pesan notifikasi
        ->success()
        ->send();
}