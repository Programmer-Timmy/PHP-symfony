<?php

namespace App\Controller;

use App\Entity\Jobs;
use App\Entity\Person;
use App\Form\JobsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class JobsController extends AbstractController
{
    #[Route('/jobs', name: 'app_jobs')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $jobs = $entityManager->getRepository(Jobs::class)->findAll();
        return $this->render('jobs/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('jobs/create', name: 'create_jobs')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $jobs = new Jobs();
        $form = $this->createForm(JobsType::class, $jobs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobs);
            $entityManager->flush();

            $this->addFlash('notice', 'Submitted successfully!');

            return $this->redirectToRoute('app_jobs');
        }

        return $this->render('jobs/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('jobs/delete/{id}', name: 'delete_job')]
    public function delete(EntityManagerInterface $entityManager, $id)
    {
        $person = $entityManager->getRepository(Person::class)->findOneBy(['jobid' => $id]);

        if ($person === null) {
            $job = $entityManager->getRepository(Jobs::class)->find($id);

            if ($job !== null) {
                $entityManager->remove($job);
                $entityManager->flush();

                $this->addFlash('notice', 'Deleted successfully!');
            } else {
                $this->addFlash('error', 'Job not found!');
            }
        } else {
            $this->addFlash('error', 'Job ID is associated with a person. Delete the associated person first.');
        }

        return $this->redirectToRoute('app_jobs');
    }
}
