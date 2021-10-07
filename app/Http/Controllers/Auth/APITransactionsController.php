<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Http\Resources\TransactionResource;
use App\Media;
use App\Receipt;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class APITransactionsController extends Controller {

    public function index(Request $request){

        $input = $request->all();
        $user = Auth::user();

        $qb = Transaction::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'data' => $data
        ];
    }

    private function saveImage($img,string $prefix, float $percentage,array $result){

        $img->resize($img->width()*$percentage, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $path = $result['path']."/$prefix-".$result['name'];
        $img->save($path, 60);

        return $path;
    }

    public function add(Request $request){

        $user = Auth::user();

        $input = $request->all();

        $validator = Validator::make($input,[
            'receipt_id' => 'required',
            'details' => 'required',
            'amount' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $receipt = Receipt::findOrFail($input['receipt_id']);
        $media_id = 0;

        if($request->media){
            $img = Image::make($request->media);
            $result = FileHelper::getAndCreatePath($img->filename,'transactions');
            $extension = substr($img->mime,strpos($img->mime,'/')+1);
            $result['name'] = $result['name'].str_random(15).'.'.$extension;

            $path = $this->saveImage($img,'default',1,$result);
            $medium = $this->saveImage($img,'medium',0.75,$result);
            $small = $this->saveImage($img,'small',0.40,$result);

            $media = Media::create([
                'name' => $result['name'],
                'path' => $path,
                'small' => $small,
                'medium' => $medium,
            ]);
            $media_id = $media->id;
        }

        $transaction = Transaction::create([
            'amount' => $input['amount'],
            'details' => $input['details'],
            'status' => Transaction::$PENDING,
            'receipt_id' => $input['receipt_id'],
            'user_id' => $user->id,
            'nook_id' => $receipt->nook_id,
            'media_id' => $media_id,
        ]);

        return [
            'message' => 'Payment submitted successfully',
            'transaction' => TransactionResource::make($transaction)
        ];
    }

}
