<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception; // Import the Exception class

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function googlelogin()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function googlecallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in your database
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User already exists
                Auth::login($user);
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => encrypt('my-google')
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('dashboard'); // Redirect to the intended page

        } catch (Exception $e) {
            return redirect('login')->with('error', 'Something went wrong!');
        }
    }

}
