<?php

namespace App\Http\Resources;

use App\Transaction;
use Illuminate\Http\Resources\Json\Resource;

class TransactionResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return self::toTransaction($this);
    }

    public static function toTransaction($t){
        return [
            'id'=> $t->id,
            'status' => Transaction::$STATUS[$t->status],
            'status_key' => $t->status,
            'amount' => $t->amount,
//            'payment_method' => Transaction::$PAYMENT_METHODS[$t->payment_method],
            "media" => ($t->media)?env("APP_URL").'/'.$t->media->path:null,
            'details' => $t->details,
            'created_at' => $t->created_at->format('g:i A, d M Y'),
            'updated_at' => $t->updated_at->format('g:i A, d M Y'),
            'user' => UserResource::make($t->user),
            'receipt' => ReceiptResource::make($t->receipt),
            'nook' => NooksResource::make($t->nook),
        ];
    }

}
