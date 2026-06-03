<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function loadDashboard(Request $request)
    {
        $users = User::whereNotIn('id', [auth()->id()])->get();
        return view('dashboard', compact('users'));
    }
}
