<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CorruptionRepository;
use App\geo\Objects\Bounds;

class GpsMapController extends AbstractController
{
    #[Route('/gpsmap', name: 'app_gps_map')]
    public function index(): Response
    {
        return $this->render('gps_map/index.html.twig', [
            'controller_name' => 'GpsMapController',
        ]);
    }

    #[Route('/gpsmap-bounds', name: 'app_gps_map_bounds', methods:"POST")]
    public function getPointsInBounds(Request $request, CorruptionRepository $repository):JsonResponse {
        $bounds = $request->getContent();
        $bounds = json_decode($bounds)->bounds;

        $queryResult = $repository->getCountByBounds(new Bounds(
            $bounds->_southWest->lat,
            $bounds->_southWest->lng,
            $bounds->_northEast->lat,
            $bounds->_northEast->lng
        ));

        return $this->json([
            'success' => true,
            'side' => 'server',
            'bounds' => $bounds,
            'queryResult' => $queryResult,
        ]);
    }
}
