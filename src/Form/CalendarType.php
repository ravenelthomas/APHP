<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
class CalendarType extends AbstractType
{
    private $security;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_')
            ->add('end_')
            ->add('title')
            ->add('description')
            ->add('categories');

        $user = $this->security->getUser();

        $builder->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => function ($user) {
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return $user->getName() . ' ' . $user->getSurname();
                } else {
                    if ($user->getId() == $this->security->getUser()->getId()) {
                        return $user->getName() . ' ' . $user->getSurname() . ' (me)';
                    }
                }
            },
            'multiple' => false,
            'expanded' => false,
            'label' => 'User',
            'required' => true,
            'choices' => $this->getUserChoices(),
        ]);
    }

    private function getUserChoices()
    {
        $user = $this->security->getUser();
        $choices = [];

        if ($this->security->isGranted('ROLE_ADMIN')) {
            // Chargez tous les utilisateurs si l'utilisateur connecté est un administrateur
            $userRepository = $this->doctrine->getRepository(User::class);
            $choices = $userRepository->findAll();
        } else {
            // Sinon, ajoutez simplement l'utilisateur connecté aux choix
            $choices[] = $user;
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'isAdmin' => false,
        ]);
    }
}
