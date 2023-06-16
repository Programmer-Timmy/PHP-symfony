<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Country;
use App\Repository\JobsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Jobs;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;


class PersonType extends AbstractType
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityManager = $this->entityManager;

        // Step 1: Retrieve the list of jobs from the database table
        $jobs = $entityManager->getRepository(Jobs::class)->findAll();
        $Countrys = $entityManager->getRepository(Country::class)->findAll();

        // Step 2: Transform the list into the format expected by the dropdown field
        $jobChoices = [];
        foreach ($jobs as $job) {
            $jobChoices[$job->getName()] = $job->getId();
        }

        $CountryChoices = [];
        foreach ($Countrys as $Country) {
            $CountryChoices[$Country->getName()] = $Country->getId();
        }


        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'Active',
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('jobid', ChoiceType::class, [
                'label' => 'Active',
                'choices' => $jobChoices,
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('countryid', ChoiceType::class, [
                'label' => 'Active',
                'choices' => $CountryChoices,
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
