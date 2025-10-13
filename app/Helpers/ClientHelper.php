<?php

namespace App\Helpers;

use App\Models\Client;

class ClientHelper
{
    /**
     * Get the currently selected client from session
     * 
     * @return Client|null
     */
    public static function getSelectedClient()
    {
        return session('selected_client');
    }

    /**
     * Get the currently selected client ID from session
     * 
     * @return int|null
     */
    public static function getSelectedClientId()
    {
        return session('selected_client_id');
    }

    /**
     * Check if a client is currently selected
     * 
     * @return bool
     */
    public static function hasSelectedClient()
    {
        return session()->has('selected_client_id');
    }

    /**
     * Set the selected client
     * 
     * @param int|Client $client
     * @return void
     */
    public static function setSelectedClient($client)
    {
        if (is_numeric($client)) {
            $client = Client::findOrFail($client);
        }
        
        session(['selected_client_id' => $client->id]);
        session(['selected_client' => $client]);
    }

    /**
     * Clear the selected client
     * 
     * @return void
     */
    public static function clearSelectedClient()
    {
        session()->forget(['selected_client_id', 'selected_client']);
    }
}

