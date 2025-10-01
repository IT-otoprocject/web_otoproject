<?php

namespace App\Http\Controllers\Access_PR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        // Apply middleware to check if user has PR access
        $this->middleware('system_access:pr');
    }

    /**
     * Display the configuration page with available options
     */
    public function index()
    {
        return view('Access_PR.configuration.index');
    }
}
