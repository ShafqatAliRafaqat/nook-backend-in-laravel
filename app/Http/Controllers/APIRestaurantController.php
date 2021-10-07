<?php

namespace App\Http\Controllers;

use App\Helpers\QB;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantsResource;
use App\Restaurant;
use App\Services\RestaurantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class APIRestaurantController extends Controller {

    public function all(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            "lat" => "required|numeric",
            "lng" => "required|numeric"
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        // working sql query

        $subQuery = "SELECT SQRT( POW(69.1 * (lat - {$input['lat']}), 2) + POW(69.1 * ({$input['lng']} - lng) * COS(lat / 57.3), 2)) from lat_lngs l WHERE restaurants.id = l.id";

        $radius = 20;

        $qb = Restaurant::with(['image','timeSlots'])->select(DB::raw("*,($subQuery) as distance,(".RestaurantService::$REVIEW_COUNT_QUERY.") as reviews_count,(".RestaurantService::$REVIEW_RATING_QUERY.") as rating"))
            ->where('status',Restaurant::$APPROVED);

        // filter
        $qb = QB::where($input,"isVeg",$qb);
        $search = $request->search;

        if($search){
            $qb->where("name", "LIKE", "%$search%");
        }

        $qb->whereHas("products", function ($q) use($request,$search){

            $value = $request->maxPrice;

            if ($value) {
                $q->where("price", "<", $value);
            }

            if($request->types){
                $q->whereIn("type_id", $request->types);
            }

            if($search){
                $q->orwhere("name", "LIKE", "%$search%");
            }

        });

        $qb = QB::whereHasIn("tags",$qb,"id",$request->tags);

        $qb = QB::hasWhere("categories",$qb,"isDeal","=",$request->withDeals);


        if($request->withPromo){
            $qb = QB::hasWhere("promos",$qb);
        }

//        $qb = $qb->whereRaw("($subQuery) < $radius")
//            ->orderBy("distance");

        $qb = $qb->whereRaw("($subQuery)")
            ->orderBy("distance");

        $restaurants = $qb->paginate();

        $restaurants->appends(Input::except('page'));

        return RestaurantsResource::collection($restaurants);

    }

    public function details($id){

        $reviewCount = "Select count(r.id) from reviews r where r.rest_id = $id";
        $reviewRating =  "SELECT sum(r.ratting)/count(r.ratting) from reviews r where r.rest_id = $id";

        $restaurant = Restaurant::with(['image','location','categories' => function($qb){
            $qb->orderBy('order')->with(['products'=>function($q){
                $q->orderBy('order')->with(['addons']);
            }]);
        },'reviews'=>function($q){
            $q->orderBy('created_at','DESC')
                ->with('user')
                ->take(30);
        },'timeSlots'=>function($l){
            $l->orderBy('day');
        }])->select(DB::raw("*,($reviewCount) as reviews_count,($reviewRating) as rating"))
            ->where('id',$id)->first();

        if(!$restaurant){
            abort(400,__("messages.restaurant.not.found",['id'=>$id]));
        }

        return new RestaurantResource($restaurant);
    }
}
