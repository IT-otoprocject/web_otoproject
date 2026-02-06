<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    /**
     * Display configuration page
     */
    public function index()
    {
        return view('admin.configuration.index');
    }
}
