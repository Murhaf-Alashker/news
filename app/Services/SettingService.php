<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Create a new class instance.
     */
    public function index(): Setting
    {
        return Setting::first()->get();
    }

    public function update(array $data): Setting
    {
        $setting = Setting::first();
        $setting->update([
            'facebook' => $data['facebook'],
            'instagram'=> $data['instagram'],
            'whatsapp' => $data['whatsapp'],
            'linkedin' => $data['linkedin'],
            'response_email' => $data['response_email'],
        ]);
        return $setting->fresh();
    }
}
