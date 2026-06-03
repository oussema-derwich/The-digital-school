<?php

namespace App\Http\Controllers;

use App\Http\Services\AlertService;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index()
    {
        return response()->json($this->alertService->getAllAlerts(), 200);
    }

    public function store(Request $request)
    {
        $alert = $this->alertService->createAlert($request->all());
        return response()->json($alert, 201);
    }

    public function update(Request $request, $id)
    {
        $alert = $this->alertService->updateAlert($id, $request->all());
        return response()->json($alert, 200);
    }
    public function toggle($id)
    {
        $alert = $this->alertService->toggleAlert($id);
        return response()->json($alert, 200);
    }
    public function destroy($id)
    {
        $this->alertService->deleteAlert($id);
        return response()->json(null, 204);
    }
}
