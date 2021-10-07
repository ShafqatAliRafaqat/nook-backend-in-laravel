<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TransactionAdminResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {

        $data =  TransactionResource::toTransaction($this);
        return array_merge($data,[
            'service_fee' => (float) $this->service_fee,
            'checkout_tr_id' => $this->checkout_tr_id,
        ]);
    }
}
