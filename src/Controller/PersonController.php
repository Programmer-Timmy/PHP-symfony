<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\CrudType;
use App\Form\PersonAddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    #[Route('/', name: 'app_person')]
    public function index(): Response
    {
        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }

    #[Route('/create', name: 'create_person')]
    public function create(Request $request){
        $person = new Person();
        $form = $this->createForm(PersonAddType::class, $person);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($person);
            $em->flush();

            $this->addFlash('notice','Submitted successfully!');
        }
        return $this->render('person/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
