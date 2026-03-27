<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    protected SettingService $settingService;
    public function __construct()
    {
        $this->settingService = new SettingService();
    }

    public function index(): Setting
    {
        return $this->settingService->index();
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $newSetting = $this->settingService->update($request->validated());
        return response()->json([
            'message' => __('updated successfully',['attribute' => 'Setting']),
            'data' => $newSetting
        ]);
    }
}
