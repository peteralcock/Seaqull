<?php

namespace App\Http\Controllers;

use App\Models\Database;
use Inertia\Inertia;

class DashBoardController extends Controller
{
    public function dashboard()
    {
        $databases = Database::all();
        return Inertia::render('Dashboard', [
            'databases' => $databases,
        ]);
    }
}
