<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 8/29/2018
 * Time: 9:55 AM
 */

namespace App\Services;


class RestaurantService {
    public static $REVIEW_COUNT_QUERY = "Select count(r.id) from reviews r where r.rest_id = restaurants.id";
    public static $REVIEW_RATING_QUERY =  "SELECT sum(r.ratting)/count(r.ratting) from reviews r where r.rest_id = restaurants.id";
}