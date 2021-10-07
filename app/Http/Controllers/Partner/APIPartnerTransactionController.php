<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\BaseController;
use App\Http\Resources\TransactionResource;
use App\Receipt;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\QB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class APIPartnerTransactionController extends BaseController {

    protected $permissions = [
        // 'index'=>'transaction-list',
        // 'update'=>'transaction-edit'
    ];

    public function index(Request $request){
        
        $user = Auth::user();

        $input = $request->all();
        
        $partnernooks = $user->partnernooks()->orderBy('updated_at','DESC')->get();
        
        $nook_id[] ='';
        
        foreach ($partnernooks as $data) {
            $nook_id[] = $data->id;
        }

        $qb = Transaction::whereIn('nook_id',$nook_id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::whereLike($input,"details",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);

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

        $trs = $qb->paginate(20);

        $trs->appends(Input::except('page'));

        return TransactionResource::collection($trs);
    }

    public function update(Request $request, $id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'status' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $transaction = Transaction::findOrFail($id);

        if($input['status'] != Transaction::$APPROVED){
            $transaction->update([
                'status' => $input['status']
            ]);
            return [
                'message' => 'Transaction Updated Successfully',
                'transaction' => TransactionResource::make($transaction)
            ];
        }

        $validator = Validator::make($input,[
            'amount' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $receipt = $transaction->receipt;

        $extras = json_decode($receipt->extras);
        $extraAmount = 0;

        foreach ($extras as $extra){
            $extraAmount+=$extra;
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

        $transaction->update([
            'status' => $input['status'],
            'amount' => $input['amount']
        ]);

        return [
            'message' => 'Transaction Updated Successfully',
            'transaction' => TransactionResource::make($transaction)
        ];
    }

}
