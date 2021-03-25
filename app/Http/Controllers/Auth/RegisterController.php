<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\PrivateUserResource;

class RegisterController extends Controller
{
    public function action(Request $request)
    {
        // return $request;
        //validation
        $this->validate($request,[
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed',
        ]);

        //store user 
       $user =  User::create([
            'name' =>$request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'password' => $request->password,
            
        ]);
        
        //sign the user in
        $credentials = $request->only('username', 'password');
        
        Auth::attempt($credentials);

        return new PrivateUserResource($user);
        
        // auth()->attempt([
        //     'email' => $request->email,
        //     'password' => $request->password
        // ]);
        // auth()->attempt($request->only('email','password'));

         //redirect
        // return redirect()->route('dashboard');
    }
}
