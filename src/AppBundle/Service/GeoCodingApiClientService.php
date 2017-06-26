<?php

namespace AppBundle\Service;

/**
 * Created by PhpStorm.
 * User: deanlovett
 * Date: 6/26/17
 * Time: 12:06 AM
 */
class GeoCodingApiClientService
{
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::         Stolen from....                                                 :*/
    /*::         http://www.geodatasource.com/developers/php                     :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    static function distance($lat1, $lon1, $lat2, $lon2, $unit = 'M')
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    //        $googleAddress = ('517 4th Ave.,San Diego,CA,92101');

    public function getLocationForAddress($addressArray)
    {
        $googleAddress = implode(',', $addressArray);
        $googleKey = 'AIzaSyCg4DOyGCsZHcKui5ikNJc9UzsvdksU4xQ';
        $parms = ['address' => $googleAddress, 'key' => $googleKey];
        $query = http_build_query($parms);
        $googleURL = 'https://maps.googleapis.com/maps/api/geocode/json?';
        $googleFull = $googleURL . $query;

        $googleClient = new \GuzzleHttp\Client();
        $res = $googleClient->request('GET', $googleFull);

        $responseJson = $res->getBody()->getContents();
        $sourceLocation = json_decode($responseJson, true)['results'][0]['geometry']['location'];
        return $sourceLocation;
    }

}