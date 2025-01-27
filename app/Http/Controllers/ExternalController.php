<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;

class ExternalController extends Controller
{
    public function activate_user($token){
        if($token!=''){
            $user=\App\Models\User::where('activation_token', $token)->first();
            if($user){
                return view('account.activate')->with(['user'=>$user]);
            }else{
                return 'User Token does not exist';
            }
        }else{
             return 'User Token does not exist';
        }
    }

    public function activate_user_save(Request $request){
        $token=$request->get('token');
        $password=$request->get('password');
        $password_confirm=$request->get('password_confirm');
        if($password==$password_confirm){
            \App\Models\User::where('activation_token', $token)->update(['password'=>Hash::make($password), 'activation_token'=>'']);
            session()->flash('message', 'Password set successfully.');
            return redirect('/login');
        }else{
            session()->flash('message', 'Password was not set successfully. Please try again.');
            return redirect()->back();
        }
    }
}
