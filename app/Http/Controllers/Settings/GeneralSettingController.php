<?php

namespace App\Http\Controllers\Settings;

use App\APIResponseBuilder;
use App\Http\Controllers\Controller;
use App\Settings\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index(GeneralSetting $setting): JsonResponse
    {
        return APIResponseBuilder::success($setting);
    }

    public function store(Request $request, GeneralSetting $setting): JsonResponse
    {
        $setting->site_name = $request->site_name ?: $setting->site_name;
        $setting->site_url = $request->site_url ?: $setting->site_url;
        $setting->locale = $request->locale ?: $setting->locale;
        $setting->timezone = $request->timezone ?: $setting->timezone;
        $setting->per_page = $request->per_page ?: $setting->per_page;
        $setting->save();
        return APIResponseBuilder::success($setting);
    }
}
