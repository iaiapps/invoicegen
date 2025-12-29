<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('user.settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'shop_name' => 'nullable|string|max:255',
            'shop_address' => 'nullable|string',
            'shop_phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->shop_name = $validated['shop_name'];
        $user->shop_address = $validated['shop_address'];
        $user->shop_phone = $validated['shop_phone'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Delete old logo if exists
        if ($user->shop_logo && Storage::disk('public')->exists($user->shop_logo)) {
            Storage::disk('public')->delete($user->shop_logo);
        }

        // Store new logo
        $path = $request->file('logo')->store('logos', 'public');

        $user->shop_logo = $path;
        $user->save();

        return redirect()->route('settings')->with('success', 'Logo berhasil diupload!');
    }
}
