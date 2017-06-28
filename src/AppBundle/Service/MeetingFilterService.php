<?php
/**
 * Created by PhpStorm.
 * User: deanlovett
 * Date: 6/27/17
 * Time: 11:49 PM
 */

namespace AppBundle\Service;


class MeetingFilterService
{
    public function filterMeetings( $meetings, $sourceLocation, $miles, $day)
    {
        $dayFilteredMeetings = $meetings;
        if ($day !== "any")
        {
            $dayFilteredMeetings = array_filter($dayFilteredMeetings, function ($item) use ($day) {
                return $item['time']['day'] === $day;
            });
        }

        $milesFilteredMeetings = array_map(
            function ($item) use ($sourceLocation) {
                $distance = call_user_func(
                    [GeoCodingApiClientService::class, 'distance'],
                    $sourceLocation['lat'],
                    $sourceLocation['lng'],
                    $item['address']['lat'],
                    $item['address']['lng']
                );
                return [
                    'distance' => $distance,
                    'id' => $item['id'],
                    'meeting_name' => $item['meeting_name'],
                    'raw_address' => $item['raw_address'],
                ];
            },
            $dayFilteredMeetings
        );


        if ($miles !== "any")
        {
            $milesFilteredMeetings = array_filter($milesFilteredMeetings, function ($item) use ($miles) {
                return $item['distance'] <= $miles;
            });
        }
        return $milesFilteredMeetings;
    }
}