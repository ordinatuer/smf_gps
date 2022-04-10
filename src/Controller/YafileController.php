<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Esception\FileEsception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Form\YafileType;
use App\Entity\Yafile;
use App\Service\YafileUploader;

use Doctrine\Persistence\ManagerRegistry;

class YafileController extends AbstractController
{
    #[Route('/yafile', name: 'app_yafile')]
    public function index(Request $request, YafileUploader $yafileUploader, ManagerRegistry $doctrine): Response
    {
        $yafile = new Yafile();
        $form = $this->createForm(YafileType::class, $yafile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                $filename = $yafileUploader->upload($file);

                $time = new \DateTime();
                $yafile->setAdded($time);
                $yafile->setName($filename);

                $doctrine->getRepository(Yafile::class)->add($yafile);
            }
        }

        return $this->render('yafile/index.html.twig', [
            'controller_name' => 'YafileController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/yafileslist', name: 'yafiles_list')]
    public function all(ManagerRegistry $doctrine):Response
    {
        $list = $doctrine->getRepository(Yafile::class)->findAll();

        return $this->render('yafile/list.html.twig', [
            'list' => $list,
        ]);
    }
}
