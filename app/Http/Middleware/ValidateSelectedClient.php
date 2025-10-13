<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ValidateSelectedClient
{
    /**
     * Handle an incoming request.
     * 
     * SECURITY: Validates that if a client is selected in session,
     * the current user still has permission to access it.
     * If not, the selected client is cleared from session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate if user is authenticated and a client is selected
        if (auth()->check() && session()->has('selected_client_id')) {
            $clientId = session('selected_client_id');
            $client = Client::find($clientId);
            
            // If client doesn't exist or user no longer has access, clear it
            if (!$client || Gate::denies('select', $client)) {
                session()->forget(['selected_client_id', 'selected_client']);
                
                // Optionally flash a message
                if (!$client) {
                    session()->flash('warning', 'The selected client no longer exists and has been cleared.');
                } else {
                    session()->flash('warning', 'You no longer have access to the selected client. Selection cleared.');
                }
            }
        }
        
        return $next($request);
    }
}
