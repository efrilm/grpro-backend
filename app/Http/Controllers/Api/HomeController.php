<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function all(Request $request) {
        $status = $request->input('status');
        
        if($status) {
            $homes = Home::where('status', $status)->get();
            return ResponseFormatter::success(
                $homes,
            );
        }

        $homes = Home::get();

        return ResponseFormatter::success(
            $homes,
        );
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block' => 'required',
            'nomer' => 'required',
            'type' => 'required',
            'project_code' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $data = [
            'block' => $request->input('block'),
            'nomer' => $request->input('nomer'),
            'type' => $request->input('type'),
            'status' => 1,
            'project_code' => $request->input('project_code'),
        ];

        $home = Home::create($data);

        if($home) {
            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message'=> 'This home has been Stored',
                    'data' => $home,
                ],
                'Succesfully',
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                500
            );
        }
    }
}
