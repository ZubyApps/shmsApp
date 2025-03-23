<?php

namespace App\Http\Controllers;

use App\Services\DatatablesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        )
    {
        
    }

    public function index()
    {
        return view('admin.admin');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function preSearchSettings(Request $request)
    {
        return Cache::put('preSearch', $request->preSearch);
    }

    public function nursingBenchMarkSetting(Request $request)
    {
        return Cache::put('nursingBenchmark', $request->preSearch);
    }
    public function loadOtherSettings(Request $request)
    {
        $otherSettings = [];
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        $otherSettings[0] = ["name" => "Pre Search", "value" => Cache::get("preSearch") ?? "Not Set", "desc" => "Change search behavior to prefetch the patient before searching for patients visit details"];
        $otherSettings[1] = ["name" => "Nursing Performance Benchmark", "value" => Cache::get("nursingBenchmark", 30), "desc" => "Set the benchmark for calculating the nursing performance"];
        // info($otherSettings);
        return response()->json([
            'data' => $otherSettings,
            'draw' => $params->draw,
            'recordsTotal' => count($otherSettings),
            'recordsFiltered' => count($otherSettings)
        ]);

    }
}
