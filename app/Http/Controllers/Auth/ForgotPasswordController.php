<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    // Email tujuan tetap
    protected $fixedEmail = 'hakimofa0910@gmail.com';

    /**
     * Kirim link reset password ke email tetap
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi email tetap (opsional, bisa diisi apa saja)
        $request->validate(['email' => 'required|email']);

        // Cari user berdasarkan email yang dimasukkan (boleh fiktif)
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            // Jika user tidak ditemukan, tetap kirim ke email tetap
            // Tapi kita buat user baru sementara? Atau langsung kirim?

            // Opsi: Buat token reset untuk email yang dimasukkan
            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => bcrypt($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Kirim email ke email tetap dengan link untuk email asli
            $this->sendResetEmail($request->email, $token);

            return back()->with('status', 'Jika email terdaftar, link reset password akan dikirim ke hakimofa0910@gmail.com');
        }

        // Jika user ditemukan, generate token
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => bcrypt($token),
                'created_at' => Carbon::now()
            ]
        );

        // Kirim ke email tetap
        $this->sendResetEmail($user->email, $token);

        return back()->with('status', 'Link reset password telah dikirim ke hakimofa0910@gmail.com');
    }

    /**
     * Kirim email reset password ke email tetap
     */
    protected function sendResetEmail($originalEmail, $token)
    {
        $resetUrl = url(config('app.url') . route('password.reset', [
            'token' => $token,
            'email' => $originalEmail
        ], false));

        // Data untuk email
        $data = [
            'originalEmail' => $originalEmail,
            'resetUrl' => $resetUrl,
            'token' => $token
        ];

        // Kirim email ke hakimofa0910@gmail.com
        Mail::send('emails.custom-reset', $data, function ($message) {
            $message->to($this->fixedEmail)
                ->subject('Reset Password - ' . config('app.name'));
        });
    }
}
