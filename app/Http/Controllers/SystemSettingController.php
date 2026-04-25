<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    private function normalizeSettingValue(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Keep external URLs untouched, but persist app files as relative paths.
        if (preg_match('/^https?:\/\//i', $value)) {
            $appUrl = rtrim(config('app.url', ''), '/');
            if ($appUrl !== '' && str_starts_with($value, $appUrl . '/')) {
                return ltrim(substr($value, strlen($appUrl)), '/');
            }

            return $value;
        }

        return ltrim($value, '/');
    }

    private function resolveSettingAssetUrl(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        // Add default values if not present.
        if (!isset($settings['app_name'])) {
            $settings['app_name'] = config('app.name', 'WakMusic');
        }
        if (!isset($settings['brand_logo'])) {
            $settings['brand_logo'] = 'brand/brand_logo_1767754045.svg';
        }
        if (!isset($settings['brand_text'])) {
            $settings['brand_text'] = 'brand/brand_text_1767754037.svg';
        }
        if (!isset($settings['hero_background'])) {
            $settings['hero_background'] = 'brand/hero_bg.jpg';
        }

        foreach (['brand_logo', 'brand_text', 'hero_background'] as $assetKey) {
            if (isset($settings[$assetKey])) {
                $settings[$assetKey] = $this->resolveSettingAssetUrl($settings[$assetKey]);
            }
        }

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        
        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $this->normalizeSettingValue($value)]
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
            $filename = $request->type . '.' . $extension;

            // Store directly in public/brand
            if (!is_dir(public_path('brand'))) {
                mkdir(public_path('brand'), 0755, true);
            }
            $file->move(public_path('brand'), $filename);
            $relativePath = 'brand/' . $filename;

            SystemSetting::updateOrCreate(
                ['key' => $request->type],
                ['value' => $relativePath]
            );

            return response()->json(['url' => asset($relativePath)]);
        }

        return response()->json(['message' => 'Upload failed'], 400);
    }
}
