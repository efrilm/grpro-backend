<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Tracking;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{

    public function all(Request $request)
    {

        $status = $request->input('status');
        $salesId = $request->input('sales_id');
        $createdBy = $request->input('created_by');
        $projectCode = $request->input('project_code');


        if ($status) {
            $visits = Visit::with(['lead', 'sales', 'createBy'])->where('status', $status)->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $visits,
            );
        } else if ($salesId) {
            $visits = Visit::with(['lead', 'sales', 'createBy'])->where(['sales_id' => $salesId, 'status' => $status, 'project_code' => $projectCode])->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $visits,
            );
        } else if ($createdBy) {
            $visits = Visit::with(['lead', 'sales', 'createBy'])->where(['created_by' => $createdBy, 'status' => $status, 'project_code' => $projectCode])->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $visits,
            );
        }


        $visits = Visit::with(['lead', 'sales', 'createBy'])->orderBy('updated_at', 'desc')->get();

        return ResponseFormatter::success(
            $visits,
        );
    }



    public function reschedule(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lead_id' => 'required',
            'user_id' => 'required',
            'visit_date' => 'required',
        ]);

        $id = $request->input('id');
        $leadId = $request->input('lead_id');
        $userId = $request->input('user_id');
        $note = "Sales Telah Mengubah Jadwal Visit";
        $codeTracking = Helpers::codeTracking();

        $dateVisit = $request->input('visit_date');

        $data = [
            'visit_date' => $dateVisit,
            'note' => $note,
        ];

        $update = Visit::where('id', $id)->update($data);

        if ($update) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $leadId,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Reschedule has been successfully',
                    'visit' => $data,
                    'tracking' => $tracking,
                ],
                'Success',
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                500,
            );
        }
    }
}
