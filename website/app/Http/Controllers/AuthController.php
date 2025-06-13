<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(){
        if(Auth::check()){
            return redirect('/');
        }
        return view('auth.login');
    }

    function loginPost(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $credentials = $request->only("email", "password");
        if(Auth::attempt($credentials)){
            return redirect()->route("home")->with("success", "Berhasil Login");
        }else{
            return redirect()->back()->with("error", "Gagal Login");
        }
    }

    public function register(){
        if(Auth::check()){
            return redirect('/');
        }
        return view('auth.register');
    }

    function registerPost(Request $request){
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users,email", // Tambahkan unique validation
            "password" => "required|min:6",
            "re_pass" => "required|same:password" // Perbaikan validation
        ]);

        // PERBAIKAN: Gunakan validation Laravel yang sudah ada
        $create = User::create([
            "name" => $request->name,
            "email" => $request->email,
            'email_verified_at' => now(),
            "password" => Hash::make($request->password),
        ]);

        if($create){
            $credentials = $request->only("email", "password");
            if(Auth::attempt($credentials)){
                return redirect('/')->with("success", "Berhasil membuat user sekaligus login");
            }else{
                Auth::logout();
                return redirect()->back()->with("error", "Gagal Login tetapi user berhasil di buat, silahkan login di halaman login");
            }
        }else{
            return redirect()->back()->with("error", "Gagal membuat user");
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login')->with('success', 'Berhasil Logout');
    }
}
