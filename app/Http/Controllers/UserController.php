<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with their personnel relationships
        $users = User::with('personnel')->get();

        return view('users.index', compact('users'));
    }
}
