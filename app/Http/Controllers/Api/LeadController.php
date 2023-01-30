<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\Fee;
use App\Models\Home;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Tracking;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadController extends Controller
{

    public function all(Request $request)
    {

        $status =  $request->input('status');
        $salesId = $request->input('sales_id');
        $projectCode =  $request->input('project_code');
        $createdBy = $request->input('created_by');

        if ($salesId) {
            $lead = Lead::with(['sales', 'createBy', 'fee', 'home', 'payment', 'date', 'tracking',])->where(['sales_id' => $salesId, 'status' => $status, 'project_code' => $projectCode])->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $lead,
                'Success',
            );
        } else if ($createdBy) {
            $lead = Lead::with(['sales', 'createBy', 'fee', 'home', 'payment', 'date', 'tracking',])->where(['created_by' => $createdBy, 'status' => $status, 'project_code' => $projectCode])->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $lead,
                'Success',
            );
        } else if($status) {
            $lead = Lead::with(['sales', 'createBy', 'fee', 'home', 'payment', 'date', 'tracking',])->where('status', $status)->orderBy('updated_at', 'desc')->get();
            return ResponseFormatter::success(
                $lead,
                'Success',
            );
        }

        $lead = Lead::with(['sales', 'createBy', 'fee', 'home', 'payment', 'date', 'tracking',])->orderBy('updated_at', 'desc')->get();

        return ResponseFormatter::success(
            $lead,
            'Success',
        );
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no_whatsapp' => 'required',
            'note' => 'required',
            'sales_id' => 'required',
            'created_by' => 'required',
            'project_code' => 'required',
            'source' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $name = $request->input('name');
        $noWhatsapp = $request->input('no_whatsapp');
        $address = $request->input('address');
        $note = $request->input('note');
        $salesId = $request->input('sales_id');
        $createdBy = $request->input('created_by');
        $projectCode = $request->input('project_code');
        $source = $request->input('source');

        $prefix = 'LDGR' . $projectCode;
        $leadCode = Helpers::autonumber('leads', 'id', $prefix);

        $lead = Lead::create([
            'lead_code' => $leadCode,
            'name' => $name,
            'no_whatsapp' => $noWhatsapp,
            'address' => $address,
            'note' => $note,
            'sales_id' => $salesId,
            'created_by' => $createdBy,
            'source' => $source,
            'project_code' => $projectCode,
            'status' => 'NEW',
            'day' => date('Y-m-d'),
        ]);

        if ($lead) {
            $dataTrack = [
                'tracking_code' => Str::random(8),
                'lead_id' => $lead->id,
                'user_id' => $createdBy,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);

            // Update Sales
            $updateSales = User::where('id', $salesId)->update(['last_queue' => date('Y-m-d H:i:s')]);

            // DATES

            $dataDates = [
                'lead_id' => $lead->id,
                'date_add' => date('Y-m-d H:i:s'),
            ];

            $dates = Date::create($dataDates);
            return ResponseFormatter::success(
                [
                    'message' => 'Added Data Successfully',
                    'lead' => $lead,
                    'tracking' => $tracking,
                    'date' => $dates,
                    'update_sales' => $updateSales,
                ],
                "success",
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                500
            );
        }
    }

    public function storeFollowUp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'note' => 'required',
                'sales_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $id = $request->input('id');
        $note = $request->input('note');
        $salesId = $request->input('sales_id');
        $codeTracking = Helpers::codeTracking();

        $data = [
            'note' => $note,
            'status' => 'FOLLOW UP',
        ];

        $update = Lead::where('id', $id)->update($data);
        if ($update) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $id,
                'user_id' => $salesId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);
            $dataDate = [
                'date_follow_up' => date('Y-m-d H:i:s'),
            ];
            $dates = Date::where('lead_id', $id)->update($dataDate);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Follow Up Successfully',
                    'lead_id' => $id,
                    'sales_id' => $id,
                    'lead' => [
                        'data' => $update,
                        'tracking' => $tracking,
                        'dates' => $dates,

                    ]
                ]
            );
        } else {
            return ResponseFormatter::error(
                "Failed",
                500
            );
        }
    }

    public function storeVisit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'date_visit' => "required",
            'note' => 'required',
            'user_id' => 'required',
            'created_by' => 'required',
            'project_code' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $id = $request->input('id');
        $dateVisit = $request->input('date_visit');
        $note = $request->input('note');
        $userId = $request->input('user_id');
        $createdBy = $request->input('created_by');
        $projectCode = $request->input('project_code');
        $codeTracking = Helpers::codeTracking();

        $prefix = 'GRVT' . $projectCode;
        $codeVisit = Helpers::autonumber('visits', 'id', $prefix);

        $data = [
            'visit_code' => $codeVisit,
            'lead_id' => $id,
            'visit_date' => $dateVisit,
            'note' => $note,
            'status' => 1,
            'sales_id' => $userId,
            'created_by' => $createdBy,
            'project_code' => $projectCode,
        ];

        $visit = Visit::create($data);

        if ($visit) {
            $dataLead = [
                'note' => $note,
                'status' => 'VISIT',
            ];

            $updateLead = Lead::where('id', $id)->update($dataLead);

            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $id,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);
            $dataDate = [
                'date_will_visit' => date('Y-m-d H:i:s'),
            ];
            $dates = Date::where('lead_id', $id)->update($dataDate);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Visit Successfully',
                    'lead_id' => $id,
                    'sales_id' => $userId,
                    'visit' => [
                        'data' => $visit,
                        'lead' => $dataLead,
                        'tracking' => $tracking,
                        'dates' => $dataDate,

                    ]
                ]
            );
        } else {
            return ResponseFormatter::error(
                "Failed",
                500,
            );
        }
    }

    public function storeAlreadyVisit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lead_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }


        $id = $request->input('id');
        $leadId = $request->input('lead_id');
        $userId = $request->input('user_id');
        $codeTracking = Helpers::codeTracking();
        $note = 'Lead Telah Visit';

        $data = [
            'status' => 2,
            'note' => $note,
        ];

        $updateVisit = Visit::where('id', $id)->update($data);

        if ($updateVisit) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $leadId,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);
            $dataDate = [
                'date_already_visit' => date('Y-m-d H:i:s'),
            ];

            $dates = Date::where('lead_id', $leadId)->update($dataDate);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Already Visit Successfully',
                    'lead_id' => $leadId,
                    'sales_id' => $userId,
                    'visit' => [
                        'data' => $data,
                        'tracking' => $tracking,
                        'dates' => $dataDate,

                    ]
                ]
            );
        } else {
            return ResponseFormatter::error(
                "Failed",
                500,
            );
        }
    }

    public function storeReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'home_id' => 'required',
            'user_id' => 'required',
            'fee_reservation' => 'required',
            'payment_method' => 'required',
            'note' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $id = $request->input('id');
        $homeId = $request->input('home_id');
        $userId = $request->input('user_id');
        $feeReservation = $request->input('fee_reservation');
        $paymentMethod = $request->input('payment_method');
        $note = $request->input('note');

        $codeTracking = Helpers::codeTracking();

        $data = [
            'note' => $note,
            'home_id' => $homeId,
            'status' => 'RESERVATION',
            'payment_method' => $paymentMethod,
        ];

        $update = Lead::where('id', $id)->update($data);

        if ($update) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $id,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);

            $dataDate = [
                'date_reservation' => date('Y-m-d H:i:s'),
            ];
            $dates = Date::where('lead_id', $id)->update($dataDate);

            $dataFee = [
                'lead_id' => $id,
                'fee_reservation' => $feeReservation,
            ];

            $fee = Fee::create($dataFee);

            $dateHome = [
                'status' => 2,
            ];

            $home = Home::where('id', $homeId)->update($dateHome);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Reservation Successfully',
                    'leads' => [
                        'data' => $data,
                        'track' => $tracking,
                        'date' => $dataDate,
                        'fee' => $fee,
                        'home' => $dateHome,
                    ]
                ],
                "Success",
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                400,
            );
        }
    }

    public function storeBooking(Request $request)
    {
        $id = $request->input('id');
        $feeBooking = $request->input('fee_booking');
        $price = $request->input('price');
        $discountPrice = $request->input('discount_price');
        $downpayment = $request->input('downpayment');
        $discountDownpayment = $request->input('discount_downpayment');
        $note = $request->input('note');
        $userId = $request->input('user_id');
        $homeId = $request->input('home_id');
        $projectCode = $request->input('project_code');
        $codeTracking = Helpers::codeTracking();

        $prefix = "TRGR" . $projectCode;
        $paymentCode = Helpers::autonumber('payments', 'id', $prefix);

        $data = [
            'note' => $note,
            'status' => 'BOOKING',
        ];

        $update = Lead::where('id', $id)->update($data);

        if ($update) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $id,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);

            $dataDate = [
                'date_booking' => date('Y-m-d H:i:s'),
            ];
            $dates = Date::where('lead_id', $id)->update($dataDate);

            $dataHome = [
                'status' => 3,
                'price' => $price,

            ];

            $home = Home::where('id', $homeId)->update($dataHome);

            $fee = Fee::where('lead_id', $id)->first();
            $total = 0;
            $feeR = $fee->fee_reservation;
            $total = (int)$feeBooking + $feeR;

            $dataFee = [
                'fee_booking' => $feeBooking,
                'total' => $total,
            ];

            $fee = Fee::where('lead_id', $id)->update($dataFee);

            if ($discountPrice != null) {
                $subtotal = $price - $discountPrice - $total;
                $dataPayment = [
                    'payment_code' => $paymentCode,
                    'lead_id' => $id,
                    'discount_price' => $discountPrice,
                    'downpayment' => 0,
                    'discount_downpayment' => 0,
                    'downpayment_paid' => 0,
                    'subtotal' => $subtotal,
                ];
            } else if ($downpayment) {
                $downpaymentPaid = (int)$downpayment - (int)$discountDownpayment;
                $subtotal = (int)$price - (int)$downpaymentPaid - (int)$total;
                $dataPayment = [
                    'payment_code' => $paymentCode,
                    'lead_id' => $id,
                    'discount_price' => 0,
                    'downpayment' => $downpayment,
                    'discount_downpayment' => $discountDownpayment,
                    'downpayment_paid' => $downpaymentPaid,
                    'subtotal' => $subtotal,
                ];
            }

            $payments = Payment::create($dataPayment);


            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Booking Successfully',
                    'leads' => [
                        'data' => $data,
                        'track' => $tracking,
                        'date' => $dataDate,
                        'fee' => [
                            $fee,
                            $dataFee,
                        ],
                        'home' => $dataHome,
                        'payment' => $dataPayment
                    ]
                ],
                "Success",
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                400,
            );
        }
    }

    public function storeSold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'home_id' => 'required',
            'user_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Failed', 500);
        }

        $id = $request->input('id');
        $homeId = $request->input('home_id');
        $userId = $request->input('user_id');
        $note = $request->input('note');
        $codeTracking = Helpers::codeTracking();

        $data = [
            'note' => $note,
            'status' => 'SOLD',


        ];
        $update  = Lead::where('id', $id)->update($data);

        if ($update) {
            $dataTrack = [
                'tracking_code' => $codeTracking,
                'lead_id' => $id,
                'user_id' => $userId,
                'note' => $note,
                'status' => 1,
            ];
            $tracking = Tracking::create($dataTrack);

            $dataDate = [
                'date_sold' => date('Y-m-d H:i:s'),
            ];
            $dates = Date::where('lead_id', $id)->update($dataDate);

            $dateHome = [
                'status' => 4,
            ];

            $home = Home::where('id', $homeId)->update($dateHome);

            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Update to Sold Successfully',
                    'leads' => [
                        'data' => $data,
                        'track' => $tracking,
                        'date' => $dataDate,
                        'home' => $dateHome,
                    ]
                ],
                "Success",
            );
        } else {
            return ResponseFormatter::error(
                'Failed',
                400,
            );
        }
    }
}
