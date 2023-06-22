<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class ChangePasswordController extends Controller
{
    public function index()
   {
        return view('changepassword.passwordchange');
   }

   public function store(Request $request)
   {
      $user = Auth::User();
      $userpassword = $user->password;

      $request->validate(
          [
              'oldpassword' => 'required',
               'newpassword' => 'required',
               'confirmpassword' => 'required'
          ]
          );

          if(!Hash::check($request->oldpassword,$userpassword))
          {
            return redirect()->route('passwordchanges.index')
            ->with('delete','Password didnot match');
          }

          if($request->oldpassword == $request->newpassword)
          {
            return redirect()->route('passwordchanges.index')
            ->with('deletess','Old Password and New Password will not be same');
          }

          if($request->newpassword != $request->confirmpassword)
            {
                return redirect()->route('passwordchanges.index')
                ->with('deletes','New Password and Confirm Password will be same');
            }


          $user->password = Hash::make($request->newpassword);
          $user->save();
          //return redirect()->route('passwordchanges.index')->with('success','Password changed successfully');
             Auth::logout();
             return redirect('login');
   }
}

?>