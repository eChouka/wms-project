<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Hash;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $account=\App\Models\User::where('id', Auth::user()->id)->first();
        return view('account.index')->with(['account'=>$account]);
    }

    public function store(Request $request){
         // Validate the input data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'current_password' => ['nullable', 'required_with:new_password', 'string', 'min:8'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if the current password is correct
        if ($request->filled('current_password') && !Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Current password does not match.');
        }

        // Update the user's details
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        // Update the password if a new password is provided
        if ($request->filled('new_password')) {
            if($request->new_password==$request->new_password_confirmation){
                $user->password = Hash::make($request->new_password);
            }else{
                return redirect()->route('account.index')->with('status', 'New password did not match!');
            }
        }

        $user->save();

        return redirect()->route('account.index')->with('status', 'Profile updated successfully!');
    }
}
