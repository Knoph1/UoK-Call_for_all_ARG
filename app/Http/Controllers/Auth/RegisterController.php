<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use App\Http\Controllers\MailingController;

class RegisterController extends Controller
{
    //
    public function showRegistrationForm()
    {
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:255',
            'pfno' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create a new user instance
        $user = new User();
        $user->name = $validatedData['fullname'];
        $user->email = $validatedData['email'];
        $user->pfno = $validatedData['pfno'];
        $user->phonenumber = $validatedData['phonenumber'];
        $user->password = Hash::make($validatedData['password']);
        $user->role = 3; //default user role guest
        $user->isadmin = 0;
        $user->isactive = 1;
        $user->save();

        // Redirect to login page with success message
        return redirect()->route('pages.login')->with('success', 'Registration successful. Please login.');
    }

    public function resetuserpassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $recipientEmail = [$user->email];
        $details = [
            'title' => 'Password Reset Request',
            'body' => 'Your password reset link is here.'
        ];

        // Create an instance of MailingController and call the sendMail function
        $mailingController = new MailingController();
        $mailresponse = $mailingController->sendMail($recipientEmail, $details);
        if ($mailresponse['issuccess']) {
            return response(['message' => 'Password reset mail sent Successfully!', 'type' => 'success']);
        } else {
            return response(['message' => $mailresponse['message'], 'type' => 'danger']);
        }
    }
}
