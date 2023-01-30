<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{

    public function all(Request $request)
    {
        $projectCode = $request->input('project_code');

        if ($projectCode) {
            $projects = Project::where('project_code', $projectCode)->first();
            return ResponseFormatter::success(
                $projects,
                'Success',
            );
        }

        $projects = Project::get();
        return ResponseFormatter::success(
            $projects,
            'Success',
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_code' => 'required',
            'name' => 'required',
            'units' => 'required',
            'remaining_units' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Register Failed', 500);
        }


        $project = Project::create([
            'project_code' => $request->input('project_code'),
            'name' => $request->input('name'),
            'units' => $request->input('units'),
            'remaining_units' => $request->input('remaining_units'),
            'code_markom' => strtoupper(Str::random(4)),
            'code_sales' => strtoupper(Str::random(4)),
            'code_owner' => strtoupper(Str::random(4)),
        ]);

        if ($project) {
            return ResponseFormatter::success([
                'project' => $project,
            ], 'Successfully Created');
        } else {
            return ResponseFormatter::error(
                ['message' => 'Something Wrong Went'],
                'Registered Failed',
                500
            );
        }
    }
}
