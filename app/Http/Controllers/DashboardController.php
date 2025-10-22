<?php

namespace App\Http\Controllers;

use App\Models\Run;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response|\Illuminate\Http\JsonResponse
    {
        $runs = Run::orderBy(column: 'created_at', direction: 'desc')->get();

        dd($runs->first()->toArray());

        //        return response()->json($runs);

        return inertia(
            component: 'dashboard',
            props: [
                'runs' => $runs,
            ]);
    }
}
