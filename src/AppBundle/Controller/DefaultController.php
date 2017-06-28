<?php

namespace AppBundle\Controller;

use AppBundle\Service\GeoCodingApiClientService;
use AppBundle\Service\MeetingsApiClientService;
use AppBundle\Service\MeetingFilterService;
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
     * @Route("/meeting/search", name="meeting-search")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function meetingSearchAction(
        Request $request
    )
    {
        return $this->render(
            'default/meeting-search-form.html.twig',
            [
            ]);
    }


    /**
     * @Route("/meeting/results", name="meeting-results")
     * @param Request $request
     * @param GeoCodingApiClientService $geocoder
     * @param MeetingsApiClientService $meetingService
     * @param MeetingFilterService $filterService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function meetingResultsAction(
        Request $request,
        GeoCodingApiClientService $geocoder,
        MeetingsApiClientService $meetingService,
        MeetingFilterService $filterService
    )
    {
        $this->targetAddress=$request->request->all();
        unset($this->targetAddress["day"]);
        unset($this->targetAddress["miles"]);

        $sourceLocation = $geocoder->getLocationForAddress($this->targetAddress);
        $responseIndexArray = $meetingService->getMeetingsNearAddress($this->targetAddress);

        $filteredIndexArray = $filterService->filterMeetings(
            $responseIndexArray,
            $sourceLocation,
            $request->request->get("miles"),
            $request->request->get("day")
        );




        $success = usort($filteredIndexArray, function ($a, $b) {
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
                'meetings' => $filteredIndexArray,
            ]);
    }
}
