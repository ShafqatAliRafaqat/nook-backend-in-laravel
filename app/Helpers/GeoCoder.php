<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 9/11/2018
 * Time: 11:05 AM
 */

namespace App\Helpers;


class GeoCoder {


    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */

    public static function distanceFromLatLng($latlng, $radius = 6371000) {

        // convert from degrees to radians

        $latFrom = deg2rad($latlng['iLat']);
        $lonFrom = deg2rad($latlng['iLng']);
        $latTo = deg2rad($latlng['lat']);
        $lonTo = deg2rad($latlng['lng']);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * $radius;
    }

}