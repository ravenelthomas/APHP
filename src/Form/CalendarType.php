<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userName = function ($user) {
            return $user->getName() . ' ' . $user->getSurname();
        };
        $builder
            ->add('start_')
            ->add('end_')
            ->add('title')
            ->add('description')
            ->add('categories')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => $userName,
                'multiple' => false,
                'expanded' => false,
                'label' => 'User',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
