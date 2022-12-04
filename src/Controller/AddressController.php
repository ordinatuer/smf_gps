<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Yafile;
use App\Form\AddressType;
use App\Repository\YafileRepository;
use App\Service\AddressService;

class AddressController extends AbstractController
{
    #[Route('/address', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'controller_name' => 'AddressController',
        ]);
    }

    #[Route('/address_load', name: 'address_load')]
    public function addressForm(
        Request $request,
        AddressService $addressService,
        UserInterface $user,
        YafileRepository $yafileRepository,
    )
    {
        $address = new Yafile();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        $message = '';
        $stat = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                $time = new \DateTime();
                $description = $form->get('description')->getData();

                $address->setAdded($time);
                $address->setFileType(Yafile::FILE_TYPES['PROVIDERS_ADDRESS_LIST']);
                $address->setYuser($user);
                $address->setDescription($description);

                $yafileRepository->uploadAddress($file, $address);

                $stat = $addressService->work($address);
                
                $message = $addressService->message;
            }
        }

        return $this->render('address/address.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'stat' => $stat,
        ]);
    }
}
