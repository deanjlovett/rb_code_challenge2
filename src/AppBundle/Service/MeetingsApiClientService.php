<?php
/**
 * Created by PhpStorm.
 * User: deanlovett
 * Date: 6/26/17
 * Time: 1:03 AM
 */

namespace AppBundle\Service;


class MeetingsApiClientService
{
    public function getMeetingsNearAddress($targetAddress)
    {

        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'POST',
            'http://tools.referralsolutionsgroup.com/meetings-api/v1/',
            [
                'auth' => ['oXO8YKJUL2X3oqSpFpZ5', 'JaiXo2lZRJVn5P4sw0bt'],
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => 1,
                    "method" => "byLocals",
                    "params" => [
                        [
                            $targetAddress,
                        ]
                    ]
                ],
            ]
        );

        $responseJson = $res->getBody()->getContents();
        $responseAssocArray = json_decode($responseJson, true);
        $responseIndexArray = $responseAssocArray['result'];
        return $responseIndexArray;
    }
}
