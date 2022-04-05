<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Form\YafileType;
use App\Entity\Yafile;

use Doctrine\Persistence\ManagerRegistry;

class YafileController extends AbstractController
{
    #[Route('/yafile', name: 'app_yafile')]
    public function index(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $yafile = new Yafile();
        $form = $this->createForm(YafileType::class, $yafile);
        $form->handleRequest($request);

        $message = "Nope";

        if ($form->isSubmitted() && $form->isValid()) {
            $message = "Load and checked";

            $file = $form->get('file')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            }

            try {
                $file->move($this->getParameter('upload_files'), $newFilename);

                $message = "Load and checked";

                $time = new \DateTime();
                $yafile->setAdded($time);
                $yafile->setName($newFilename);

                $doctrine->getRepository(Yafile::class)->add($yafile);

            } catch(FileException $fe) {
                $message = $fe->getMessage();
            }
        }

        return $this->render('yafile/index.html.twig', [
            'controller_name' => 'YafileController',
            'form' => $form->createView(),
            'message' => $message,
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
