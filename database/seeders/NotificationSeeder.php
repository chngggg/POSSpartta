<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create sample notifications
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Selamat Datang di SparttaPOS',
                'message' => 'Terima kasih telah menggunakan sistem POS kami. Silakan jelajahi fitur-fitur yang tersedia.',
                'type' => 'success',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Tips: Scan Barcode',
                'message' => 'Anda dapat menggunakan scanner barcode untuk mempercepat proses stock opname.',
                'type' => 'info',
                'is_read' => false,
            ]);
        }
    }
}
