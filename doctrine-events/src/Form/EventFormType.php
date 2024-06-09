<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'événement',
                'attr' => ['class' => 'form-control']
            ])
            ->add('datetime', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The date must be today or later.'
                    ])
                ]
            ])
            ->add('nb_max_users')
            ->add('is_public', CheckboxType::class, [
                'label' => 'Événement public',
                'required' => false
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
