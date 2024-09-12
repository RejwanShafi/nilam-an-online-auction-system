<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        Log::info('Password update attempt', ['user_id' => $request->user()->id]);

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        Log::info('Password validation passed', ['user_id' => $request->user()->id]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        Log::info('Password updated successfully', ['user_id' => $request->user()->id]);

        return back()->with('status', 'password-updated');
    }
}