<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\QB;
use App\Http\Resources\ReceiptResource;
use App\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class APIReceiptController extends Controller {
    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();

        $qb = Receipt::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'data' => ReceiptResource::collection($data)
        ];
    }
}
