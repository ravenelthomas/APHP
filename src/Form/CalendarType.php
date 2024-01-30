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
class CalendarType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_')
            ->add('end_')
            ->add('title')
            ->add('description')
            ->add('categories');

        // Obtenez l'utilisateur connecté
        $user = $this->security->getUser();

        if (!$options['isAdmin']) {
            // Si l'utilisateur n'est pas administrateur, affichez uniquement son propre nom
            $builder->add('user', TextType::class, [
                'disabled' => true,
                'data' => $user->getName() . ' ' . $user->getSurname(),
                'label' => 'User',
            ]);
        } else {
            // Si l'utilisateur est administrateur, affichez la liste complète des utilisateurs
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getName() . ' ' . $user->getSurname();
                },
                'multiple' => false,
                'expanded' => false,
                'label' => 'User',
                'required' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'isAdmin' => false,
        ]);
    }
}
