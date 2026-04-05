<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        
        // Add default values if not present
        if (!isset($settings['app_name'])) $settings['app_name'] = 'WakMusic';
        
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        
        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|mimes:svg,png,jpg,jpeg,xml|max:2048',
            'type' => 'required|string'
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->type . '_' . time() . '.' . $extension;
            
            // Store directly in public/brand
            $file->move(public_path('brand'), $filename);
            $url = asset('brand/' . $filename);

            SystemSetting::updateOrCreate(
                ['key' => $request->type],
                ['value' => $url]
            );

            return response()->json(['url' => $url]);
        }

        return response()->json(['message' => 'Upload failed'], 400);
    }
}
