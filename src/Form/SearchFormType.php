<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sites', ChoiceType::class, array(
                'choices' => array(
                    'Saint-Herblain' => 'Saint-Herblain',
                    'Saint-Sebastien' => 'Saint-Sebastien',
                    'Nantes' => 'Nantes',
                )
            ))
            ->add('searchbar', TextType::class, [
                'label' => false,
                'required' => false,
                'empty_data' => ' ',
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('dateSortie', DateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'required'=> false,
            ])
            ->add('dateCloture', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required'=> false,
            ])
            ->add('organisateur', CheckboxType::class, array(
                'label' => 'Sortie dont je suis l\'organisateur.trice',
                'required' => false,
            ))
            ->add('inscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je suis inscrit',
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('passees', CheckboxType::class, [
                'label'    => 'Sortie passées',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}