<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }
}
