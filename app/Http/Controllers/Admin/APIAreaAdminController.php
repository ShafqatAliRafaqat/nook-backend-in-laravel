<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\ComplaintResource;
use App\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\AreaResource;
use Validator;

class APIAreaAdminController extends BaseController {
    
    public function index(Request $request){
        
        $input = $request->all();

        $qb = Area::orderBy('updated_at','Desc');
        
        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"area",$qb);
        
        $areas = $qb->paginate(20);
        
        return AreaResource::collection($areas);
    }

    public function add(Request $request){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'area' => 'required',
            'sub_area' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $area = Area::create([
            "area" =>$input['area'],
            "sub_area" => json_encode($input['sub_area']),
        ]);


        return [
            'message' => 'Area created successfully',
            'area' => AreaResource::make($area)
        ];
    }
    public function edit($id){
        
        $area = Area::where('id',$id)->first();

        if(!$area){
            abort(400,__('messages.area.not.found'));
        }

        return AreaResource::make($area); 
    }
    public function update(Request $request, $id){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'area' => 'required',
            'sub_area' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        
        $area = Area::where('id',$id)->first();

        if(!$area){
            abort(400,__('messages.area.not.found'));
        }

        $area->update([
            "area" =>$input['area'],
            "sub_area" => json_encode($input['sub_area']),
        ]);


        return [
            'message' => 'Area updated successfully',
            'area' => AreaResource::make($area)
        ];        
    }
    public function delete($id){
        
        $area = Area::where('id',$id)->first();

        if(!$area){
            abort(400,__('messages.area.not.found'));
        }

        $area->delete();

        return [
            'message' => 'Area Deleted successfully',
        ];               
    }
}
