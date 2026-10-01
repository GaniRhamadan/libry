<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use RentalHub\StarterKit\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('rental-hub::profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('rental.login');
        }

        if ($request->filled('password')) {
            $currentPassword = (string) $request->input('current_password');

            if (!Hash::check($currentPassword, (string) $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => __('rental-hub::rental.current_password_incorrect'),
                ]);
            }

            $user->password = Hash::make((string) $request->input('password'));
        }

        $user->name = (string) $request->input('name');
        $user->email = (string) $request->input('email');
        $user->phone = (string) $request->input('phone');
        $user->save();

        return back()->with('success', (string) __('rental-hub::rental.profile_updated'));
    }
}
