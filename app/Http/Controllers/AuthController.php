<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   public function index()
   {
    return view('usermanagement.forgot-password');
   }

   public function change_password(Request $request)
   {
        $current_password = $request->current_password;
        $password = $request->password;
        $confirm_password = $request->confirm_password;


        if($confirm_password == $password)
        {
            $user_id = auth()->id();
            $hashedPassword = Auth::user()->getAuthPassword();

            if (Hash::check($current_password, $hashedPassword)) {
                User::where('id', $user_id)->update([
                    'password' => Hash::make(trim($password)),
                    'force_password_change' => '0',
                ]);
                return redirect('login')->with('success', "Password changed successfully, please login again.");
            }
            else
            {
                return \Redirect::back()->with('delete', "Current password doesn't matched.");
            }


        }
        else
        {
            return \Redirect::back()->with('delete', 'Your password and confirmation password do not match.');
        }




   }
}
