<?php

namespace App\Controller;




use App\Entity\Country;
use App\Entity\Jobs;
use App\Entity\Person;
use App\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    #[Route('/', name: 'app_person')]
    public function index(EntityManagerInterface $entityManager): Response
    {
         $persons = $entityManager->getRepository(Person::class)->findAll();
        $jobs = $entityManager->getRepository(Jobs::class)->findAll();
        $countries = $entityManager->getRepository(Country::class)->findAll();

        return $this->render('person/index.html.twig', [
            'persons' => $persons,
            'jobs' => $jobs,
            'countries' => $countries,
        ]);
    }

    #[Route('persons/create', name: 'create_person')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash('notice', 'Submitted successfully!');

            return $this->redirectToRoute('app_person');
        }

        return $this->render('person/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('persons/update/{id}', name: 'update_person')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $person = $entityManager->getRepository(Person::class)->find($id);
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice', 'Updated successfully!');

            return $this->redirectToRoute('app_person');
        }

        return $this->render('person/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('persons/delete/{id}', name: 'delete_person')]
    public function delete(EntityManagerInterface $entityManager, $id)
    {
        $person = $entityManager->getRepository(Person::class)->find($id);

        $entityManager->remove($person);
        $entityManager->flush();

        $this->addFlash('notice', 'Deleted successfully!');

        return $this->redirectToRoute('app_person');
    }
}
