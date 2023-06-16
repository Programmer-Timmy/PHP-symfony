<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Person;
use App\Form\CountryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class countryController extends AbstractController
{
    #[Route('/country', name: 'app_country')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $countries = $entityManager->getRepository(Country::class)->findAll();
        return $this->render('country/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('country/create', name: 'create_country')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $country = new Country();
        $form = $this->createForm(countryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('notice', 'Submitted successfully!');

            return $this->redirectToRoute('app_country');
        }

        return $this->render('country/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('country/delete/{id}', name: 'delete_country')]
    public function delete(EntityManagerInterface $entityManager, $id)
    {
        $person = $entityManager->getRepository(Person::class)->findOneBy(['countryid' => $id]);

        if ($person === null) {
            $country = $entityManager->getRepository(Country::class)->find($id);

            if ($country !== null) {
                $entityManager->remove($country);
                $entityManager->flush();

                $this->addFlash('notice', 'Deleted successfully!');
            } else {
                $this->addFlash('error', 'Job not found!');
            }
        } else {
            $this->addFlash('error', 'Job ID is associated with a person. Delete the associated person first.');
        }

        return $this->redirectToRoute('app_country');
    }
}
