<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateDebut')
            ->add('duree')
            ->add('dateCloture')
            ->add('nbInscriptionsMax')
            ->add('descriptionsInfos')
            ->add('urlPhoto')
            ->add('users')
            ->add('user')
            ->add('site')
            ->add('etat')
            ->add('lieu')
            ->add('user_sortie', EntityType::class, [
                'choice_label' => 'name',
                'class' => User::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}