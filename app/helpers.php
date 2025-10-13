<?php

use App\Helpers\ClientHelper;
use App\Models\Client;

if (!function_exists('selectedClient')) {
    /**
     * Get the currently selected client from session
     * 
     * @return Client|null
     */
    function selectedClient()
    {
        return ClientHelper::getSelectedClient();
    }
}

if (!function_exists('selectedClientId')) {
    /**
     * Get the currently selected client ID from session
     * 
     * @return int|null
     */
    function selectedClientId()
    {
        return ClientHelper::getSelectedClientId();
    }
}

if (!function_exists('hasSelectedClient')) {
    /**
     * Check if a client is currently selected
     * 
     * @return bool
     */
    function hasSelectedClient()
    {
        return ClientHelper::hasSelectedClient();
    }
}

