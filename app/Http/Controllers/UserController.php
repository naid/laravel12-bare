<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with their personnel relationships
        $users = User::with('personnel')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,manager,user',
            'client_ids' => 'nullable|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Attach clients if provided
        if (!empty($validated['client_ids'])) {
            // Default access level is 'read' for regular users, 'write' for managers
            $accessLevel = $validated['role'] === 'manager' ? 'write' : 'read';
            
            $clientData = [];
            foreach ($validated['client_ids'] as $clientId) {
                $clientData[$clientId] = ['access_level' => $accessLevel];
            }
            
            $user->clients()->attach($clientData);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user) {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emai;' => 'required|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,manager,user',
            'password' => ['nullable','confirmed',Password::min(8)],
        ]);

        //Update user data
        $user->name > $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];


        //Only Update password if password field not empty
        if(!empty($validated['password'])){
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully');
    }



    public function destroy() {
        return true;
    }
}
