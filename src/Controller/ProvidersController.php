<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use App\Repository\YafileRepository;
use App\Service\Encoders\WaypointsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class ProvidersController extends AbstractController
{
    #[Route('/providers', name: 'providers_map')]
    public function providersMap():Response
    {

        return $this->render('providers/providers.html.twig');
    }

    #[Route('/providers_files', name: 'providers_files', methods:"GET")]
    public function getFiles(TokenStorageInterface $user, YafileRepository $yafileRepository):JsonResponse
    {

        $yuid = $user->getToken()->getUser()->getId();

        $files = $yafileRepository->findByUser($yuid);

        return $this->json([
            'files' => $files,
        ]);
    }

    #[Route('/providers_address_list', name: 'address_list', methods:"GET")]
    public function getAddressList(Request $request, AddressRepository $addressRepository):JsonResponse
    {
        $fileId = (int)$request->query->get('file_id');
        $list = $addressRepository->getAddressList($fileId);

        return $this->json([
            'file_id' => $fileId,
            'list' => $list,
        ]);
    }

    #[Route('/providers_get_file', name: 'providers_get_file', methods: 'GET')]
    public function getAddressFile(
        Request $request,
        AddressRepository $addressRepository,
        YafileRepository $yafileRepository,
        WaypointsService $waypointsService):BinaryFileResponse
    {
        $fileId = (int)$request->query->get('file_id');
        // $fileId = 637;
        // $fileId = 532;
        $file = $yafileRepository->find($fileId);

        if (!$this->isGranted('file', $file)) {
            throw $this->createAccessDeniedException('No file access!');
        }

        $addressList = $addressRepository->getAddressList($fileId);
        $data = $waypointsService->encode($addressList);
        $data = [
            // '@creator' => 'Health',
            '@xsi:schemaLocation' => 'http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd',
            '@version' => '1.0',
            '@xmlns' => 'http://www.topografix.com/GPX/1/0',
            '@xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            '#' => $data,
        ];

        $xmlEncoder = new XmlEncoder([
            'xml_version' => 1.0,
            'xml_encoding' => 'utf-8',
            'xml_standalone' => 'yes',
        ]);
        $content = $xmlEncoder->encode($data, XmlEncoder::FORMAT, [
            'xml_root_node_name' => 'gpx',
        ]);

        // $tmpFileName = $this->getParameter('download_files') . '/' . uniqid('gpxwp_');
        $tmpFileName = (new Filesystem())->tempnam(sys_get_temp_dir(), 'gpxwp_');
        $tmpFile = fopen($tmpFileName, 'wb+');

        fwrite($tmpFile, $content);

        $response = $this->file($tmpFileName, 'Пойнты.gpx');
        $response->headers->set('Content-type', 'text/xml; charset=utf-8');

    
        return $response;
    }
}
