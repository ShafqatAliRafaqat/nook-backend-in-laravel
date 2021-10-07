<?php

namespace App\Http\Controllers;

use App\Helpers\QB;
use App\Http\Resources\NooksResource;
use App\Nook;
use Illuminate\Http\Request;

class APINookController extends Controller {
    public function index(Request $request){

        $input = $request->all();

        $qb = Nook::orderBy('id')->with(['location','medias','rooms'])
            ->where('status',Nook::$APPROVED)
            ->whereHas('partner', function ($q) use($request) {
                    $q->where('is_active', 1);
            });
        $qb = QB::whereLike($input,"description",$qb);
        $qb = QB::whereLike($input,"facilities",$qb);
        $qb = QB::where($input,"type",$qb);
        $qb = QB::where($input,"space_type",$qb);
        $qb = QB::where($input,"gender_type",$qb);
        $qb = QB::where($input,"nookCode",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $nooks = $qb->get();

        return NooksResource::collection($nooks);
    }
}
