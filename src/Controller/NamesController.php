<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Names;
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

    #[Route('/addname', name: 'addname')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $name = new Names();

        $form = $this->createFormBuilder($name)
            ->add('name', TextType::class, ['label' => 'Имя'])
            ->add('lastname', TextType::class, ['label' => 'Фамилиё'])
            ->add('save', SubmitType::class, ['label' => 'Добавить'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$name = $form->getData();
            $nameName = $form->getData()->getName();

            $name->setName($nameName);
            $name->setLastname($form->getData()->getLastname());

            $repository = $doctrine->getRepository(Names::class);
            $repository->add($name);

            // $manager = $doctrine->getManager();
            // $manager->persist($name);
            // $manager->flush();

            return $this->redirectToRoute('app_allnames');
        }


        return $this->render('names/add.html.twig', [
            'form' => $form->createView(),
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
