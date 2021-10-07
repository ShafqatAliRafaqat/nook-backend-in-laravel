<?php

namespace App\Http\Controllers\Admin;

use App\Bookings;
use App\Helpers\QB;
use App\Http\Resources\ReceiptResource;
use App\Receipt;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;

class APIAdminReceiptController extends Controller {

    public function index(Request $request){
        $input = $request->all();

        $qb = Receipt::orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"month",$qb);
        $qb = QB::whereLike($input,"status",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);
        $qb = QB::where($input,"room_id",$qb);

        $qb = $qb->with([
            'user','nook'
        ]);


        $qb = $qb->whereHas('nook', function ($q) use($request) {
            if ($request->space_type) {
                $q->where('space_type', $request->space_type);
            }
            if ($request->nookCode) {
                $q->where('nookCode', $request->nookCode);
            }
        });

        $qb = $qb->whereHas('user', function ($q) use($request) {
            if ($request->number) {
                $q->where('number', $request->number);
            }
            if ($request->email) {
                $q->where('email', $request->email);
            }
        });

        $all = 0;

        if(isset($input['all'])){
            $all = $input['all'];
        }

        if($all){
            $nooks = $qb->get();
        }else{
            $nooks = $qb->paginate(15);
            $nooks->appends(Input::except('page'));
        }

        return ReceiptResource::collection($nooks);
    }

    public function publishReceipt(Request $request){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'nook_id' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $qb = Receipt::where('status',Receipt::$DRAFT)->where('nook_id',$input['nook_id']);

        $model = 'Receipts';

        if($request->receipt_id){
            $qb = $qb->where('id',$input['receipt_id']);
            $model = 'Receipt';
        }

        $qb->update([
            'status' =>  Receipt::$IN_PROGRESS,
        ]);

        return [
            'message' => $model . ' Published Successfully'
        ];
    }


    public function payReceipt(Request $request, $id){

        $receipt = Receipt::findOrFail($id);

        $input = $request->all();

        $validator = Validator::make($input,[
            'amount' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $extras = json_decode($receipt->extras);
        $extraAmount = 0;

        foreach ($extras as $extra){
            $extraAmount += $extra;
        }

        $amount = $receipt->rent + $receipt->arrears + ($receipt->e_units * $receipt->e_unit_cost) + $receipt->fine + $extraAmount;

        $currentDay = Carbon::now()->format('j');
        $dueDateDay = $receipt->due_date->format('j');
        $latePaymentCharges = 0;

        if($dueDateDay < $currentDay){
            $latePaymentCharges = ($currentDay - $dueDateDay) * $receipt->late_day_fine;
        }

        $total_amount = $amount + $latePaymentCharges;

        $arrears = $total_amount - $input['amount'];

        $receipt->update([
            'status' => Receipt::$PAID,
            'amount' => $amount,
            'latePaymentCharges' => $latePaymentCharges,
            'total_amount' => $total_amount,
            'remaining_payable' => $arrears,
            'received_amount' => $input['amount']
        ]);

        NotificationsHelper::SEND([
            'title' => 'Receipt Updated',
            'body' => 'Your receipt is marked as paid.',
        ],$receipt->user_id, env("APP_ID"));

        return [
            'message' => 'Receipt Paid Successfully',        
            'receipt' => ReceiptResource::make($receipt)
        ];
    }

    public function generateReceipt(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'user_id' => 'required',
            'e_units' => 'present',
            'e_unit_cost' => 'present',
            'fine' => 'present',
            'late_day_fine' => 'present',
            'extras' => 'present',
            'due_date' => 'required',
            'status' => 'required|in:unpaid,draft,paid,in_progress',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        if(isset($input['nook_id'])){
            $booking = Bookings::where('status', Bookings::$APPROVED)->where('user_id',$input['user_id'])->where('nook_id',$input['nook_id'])->first();
        }else{
            $booking = Bookings::where('status', Bookings::$APPROVED)->where('user_id',$input['user_id'])->first();
        }
        

        $oldReceipt = Receipt::where('status',Receipt::$IN_PROGRESS)->where('user_id',$input['user_id'])->first();
        $arrears = 0;

        if($oldReceipt){

            $extras = json_decode($oldReceipt->extras);
            $extraAmount = 0;

            foreach ($extras as $extra){
                $extraAmount+=$extra;
            }

            $amount = $oldReceipt->rent + $oldReceipt->arrears + ($oldReceipt->e_units * $oldReceipt->e_unit_cost) + $oldReceipt->fine + $extraAmount;

            $latePaymentCharges = 0;

            $lateDays = 30 - $oldReceipt->due_date->format('j');

            if($lateDays > 0){
                $latePaymentCharges = $lateDays * $oldReceipt->late_day_fine;
            }

            $total_amount = $amount + $latePaymentCharges;

            $arrears = $total_amount ;

            $oldReceipt->update([
                'status' => Receipt::$UNPAID,
                'amount' => $amount,
                'latePaymentCharges' => $latePaymentCharges,
                'total_amount' => $total_amount,
                'remaining_payable' => $arrears,
            ]);
        }

        $oldPaidReceipt = Receipt::where('status',Receipt::$PAID)->where('user_id',$input['user_id'])->orderBy('created_at', 'desc')->first();

        if($oldPaidReceipt){
            $arrears = $arrears + $oldPaidReceipt->remaining_payable;
        }

        if(!$booking){
            abort(400,'Booking not found or booking is not approved');
        }

        $currentDate = Carbon::now();

        $receipt = Receipt::create([
            'month' => $currentDate->format('m'),
            'rent' => $booking->rent,       //
            'arrears' => $arrears,          //
            'e_units' => $input['e_units'], //
            'e_unit_cost' => $input['e_unit_cost'],
            'fine' => $input['fine'],       //
            'amount' => 0, // calculate at run time
            'latePaymentCharges' => 0,  
            'extras' => json_encode($input['extras']),  //
            'total_amount' => 0, // calculate at run time
            'received_amount' => 0,
            'remaining_payable' => 0, // calculate at run time
            'late_day_fine' => $input['late_day_fine'], //
            'due_date' => Carbon::createFromTimestamp($input['due_date']),  //
            'status' => $input['status'],   //
            'user_id' => $booking->user_id,
            'nook_id' => $booking->nook_id,
            'room_id' => $booking->room_id,
        ]);

        return [
            'message' => 'Receipt Generated Successfully',
            'receipt' => ReceiptResource::make($receipt),
        ];
    }
    public function update(Request $request, $id){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'e_units'   => 'required',
            'rent'      => 'required',
            'arrears'   => 'required',
            'fine'      => 'required',
            'extras'    => 'present',
            'due_date'  => 'required',
            'status'    => 'required|in:unpaid,draft,paid,in_progress',
            'late_day_fine' => 'required',

        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $receipt = Receipt::findorFail($id);

        $receipt->update([
            'rent'      => $input['rent'],       //
            'arrears'   => $input['arrears'],          //
            'e_units'   => $input['e_units'], //
            'fine'      => $input['fine'],       //
            'extras'    => json_encode($input['extras']),  //
            'due_date'  => $input['due_date'],  //
            'status'    => $input['status'],   //
            'late_day_fine' => $input['late_day_fine'], //
        ]);

        $receipt = Receipt::findorFail($id);
       
        return [
            'message' => 'Receipt Updated Successfully',
            'receipt' => ReceiptResource::make($receipt),
        ];        
    }
    public function delete($id){
        
        $receipt = Receipt::findorFail($id);

        $receipt->delete();

        return [
            'message' => 'Receipt Deleted successfully',
        ];               
    }
}
