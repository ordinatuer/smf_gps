<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Names;
use App\Entity\Corruption;
use App\geo\Objects\Point;

class NamesController extends AbstractController
{
    #[Route('/names', name: 'app_names')]
    public function index(): Response
    {
        return $this->render('names/index.html.twig', [
            'controller_name' => 'NamesController',
        ]);
    }

    #[Route('/allnames', name: 'app_allnames')]
    public function all(ManagerRegistry $doctrine): Response
    {   
        $names = $doctrine->getRepository(Names::class)->findAll();

        



        return $this->render('names/all.html.twig', [
            'names' => $names,
            //'Yaid' => $c->getId(),
        ]);
    }
}
