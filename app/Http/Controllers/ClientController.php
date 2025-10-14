<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller 
{
    public function index()
    {
        // Authorization check
        $this->authorize('viewAny', Client::class);
        
        // Fetch only clients that the user has access to
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admins can see all clients
            $clients = Client::with('personnel')->get();
        } else {
            // Regular users only see their assigned clients
            $clients = $user->clients()->with('personnel')->get();
            
            // If no clients assigned, show all for now (you can change this later)
            if ($clients->isEmpty()) {
                $clients = Client::with('personnel')->get();
            }
        }
        
        return view('clients.index', compact('clients'));
    }

    /**
     * Set the selected client in session
     * This client will be globally accessible across all controllers
     * SECURITY: Only allows selecting clients the user has permission to access
     */
    public function setSelectedClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // SECURITY CHECK: Verify user has permission to select this client
        $this->authorize('select', $client);
        
        // Store the selected client ID in the session
        session(['selected_client_id' => $client->id]);
        
        // Optionally store client data to avoid repeated queries
        session(['selected_client' => $client]);
        
        return redirect()->back()->with('success', 'Client "' . $client->name . '" selected');
    }

    /**
     * Clear the selected client from session
     */
    public function clearSelectedClient()
    {
        session()->forget(['selected_client_id', 'selected_client']);
        
        return redirect()->back()->with('success', 'Client selection cleared');
    }
}
