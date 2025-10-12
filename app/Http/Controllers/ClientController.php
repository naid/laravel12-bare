<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController 
{
    public function index()
    {
        // Fetch all clients with their personnel relationships
        $clients = Client::with('personnel')->get();
        
        return view('clients.index', compact('clients'));
    }
}
