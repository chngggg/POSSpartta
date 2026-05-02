<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('settings.profile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('settings.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('settings.profile')
            ->with('success', 'Password berhasil diubah!');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupload!',
            'avatar_url' => Storage::url($path)
        ]);
    }
}
