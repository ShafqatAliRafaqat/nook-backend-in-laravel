<?php

namespace App\Http\Resources;

use App\Receipt;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ReceiptResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {

        $extras = json_decode($this->extras);
        $extraAmount = 0;
        $extra_array = [];
        if($extras){
            foreach ($extras as $key => $extra){
                $extraAmount+=$extra;
                $extra_array[] = (object) array("name" => $key, "value" => $extra);
            }
        }
        $amount = $this->rent + $this->arrears + ($this->e_units * $this->e_unit_cost) + $this->fine + $extraAmount;
        $total_amount = $amount + $this->latePaymentCharges;

        $remaining_payable = $total_amount - $this->received_amount;
        
        return [
            'id' => $this->id,
            'status' => isset(Receipt::$STATUS[$this->status]) ? Receipt::$STATUS[$this->status]:$this->status,
            'key_status' => $this->status,
            'month' => $this->month,
            'rent' => $this->rent,
            'arrears' => $this->arrears,
            'e_units' => $this->e_units,
            'e_unit_cost' => $this->e_unit_cost,
            'fine' => $this->fine,
            'extras' => $extras,
            'extra_array' => $extra_array,
            'amount' => $amount,
            'latePaymentCharges' => $this->latePaymentCharges,
            'total_amount' => $total_amount,
            'received_amount' => $this->received_amount,
            'remaining_payable' => $remaining_payable,
            'late_day_fine' => $this->late_day_fine,
            'due_date_format' => $this->due_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('d M Y'),
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => UserResource::make($this->user),
            'nook' => NooksResource::make($this->nook),
            'transaction' => ($this->transaction) ? $this->transaction : null,
        ];
    }
}
