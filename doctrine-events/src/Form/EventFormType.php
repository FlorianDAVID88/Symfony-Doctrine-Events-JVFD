<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Place;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                    new NotBlank(), // Pour s'assurer que le champ n'est pas vide
                    new GreaterThan([
                        'value' => new DateTimeImmutable(), // La date actuelle
                        'message' => 'La date doit être supérieure à la date d\'aujourd\'hui.'
                    ]),
                ],
            ])
            ->add('nb_max_users')
            ->add('is_public', CheckboxType::class, [
                'label' => 'Événement public',
                'required' => false,
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
