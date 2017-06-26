<?php

namespace AppBundle\Controller;

use AppBundle\Service\GeoCodingApiClientService;
use AppBundle\Service\MeetingsApiClientService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    protected $targetAddress = [
        "street" => "517 4th Ave.",
        "state_abbr" => "CA",
        "city" => "San Diego",
        "zip" => "92101"
    ];

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param GeoCodingApiClientService $geocoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(
        Request $request,
        GeoCodingApiClientService $geocoder,
        MeetingsApiClientService $meetingService
    )
    {
        $sourceLocation = $geocoder->getLocationForAddress($this->targetAddress);
        $responseIndexArray = $meetingService->getMeetingsNearAddress($this->targetAddress);
        $mondayMeetings = array_filter($responseIndexArray, function ($item) {
            return $item['time']['day'] === 'monday';
        });
        $customArray = array_map(
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
            $mondayMeetings);

        $sortCustomArray = usort($customArray, function ($a, $b) {
            if ($a['distance'] === $b['distance']) {
                return 0;
            }
            if ($a['distance'] > $b['distance']) {
                return 1;
            }
            return -1;
        });

        return $this->render(
            'default/index.html.twig',
            [
                'meetings' => $customArray,
            ]);
    }
}
