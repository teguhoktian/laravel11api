<?php

namespace App\Http\Controllers;

use App\APIResponseBuilder;
use App\Models\LogActivity;
use App\Settings\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, GeneralSetting $settings): JsonResponse
    {
        $request->merge([
            'field' => $request->input('field', 'id'),
            'direction' => $request->input('direction', 'DESC'),
            'per_page' => $request->input('per_page', $settings->per_page),
        ]);

        $activities = LogActivity::select(['id', 'log_name', 'description', 'subject_type', 'subject_id', 'causer_id'])->search($request->search)->orderBy($request->field, $request->direction)->paginate($request->per_page);;

        return APIResponseBuilder::success([
            'collections' => $activities,
            'filters' => request()->all(['search', 'per_page', 'field', 'direction'])
        ]);
    }
}
