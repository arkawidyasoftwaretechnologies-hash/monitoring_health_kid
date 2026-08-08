<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    }

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Kredensial yang diberikan tidak cocok dengan catatan kami.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest'); // We need a guest layout!
    }
}
