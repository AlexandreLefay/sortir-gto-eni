<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'required'=> false,
            ])
            ->add('duree',null, ['label' => 'Durée en H'])
            ->add('dateCloture', DateTimeType::class, [
                'widget' => 'single_text',
                'required'=> false,
            ])
            ->add('nbInscriptionsMax')
            ->add('descriptionsInfos')
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier la sortie'])
            ->add('lieu', EntityType::class, [
                'choice_label' => 'nom',
                'class' => Lieu::class,
            ]);
//            ->add('urlPhoto')
//            ->add('users')
//            ->add('user')
//            ->add('site')
//            ->add('etat')
//            ->add('lieu')
//            ->add('user_sortie', EntityType::class, [
//                'choice_label' => 'name',
//                'class' => User::class,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
