<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        // Example: Using the global selected client
        // You can access the selected client in multiple ways:
        
        // Method 1: Using helper functions (recommended)
        if (hasSelectedClient()) {
            $selectedClient = selectedClient();
            // Filter personnel by selected client
            $personnel = Personnel::with(['client', 'user'])
                ->where('client_id', selectedClientId())
                ->get();
        } else {
            // Show all personnel if no client is selected
            $personnel = Personnel::with(['client', 'user'])->get();
        }
        
        // Method 2: Using session directly
        // $selectedClientId = session('selected_client_id');
        // $selectedClient = session('selected_client');
        
        // Method 3: Using the ClientHelper class
        // use App\Helpers\ClientHelper;
        // $selectedClient = ClientHelper::getSelectedClient();
        
        return view('personnel.index', compact('personnel'));
    }

    public function create()
    {
        return view('personnel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
        ]);

        Personnel::create($validated);

        return redirect()
            ->route('personnel.index')
            ->with('success', 'Personnel created successfully');
    }

    public function show($id)
    {
        $personnel = Personnel::findOrFail($id);
        return view('personnel.show', compact('personnel'));
    }
    
}
