<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Médecine générale' => 'Médecine générale',
                    'Médecine dentaire' => 'Médecine dentaire',
                    'Pédiatrie' => 'Pédiatrie',
                    'Médecine interne' => 'Médecine interne',
                    'Je ne sais pas' => 'Je ne sais pas',

                ],
                'data_class' => null,

            ])
            ->add('hide_name', CheckboxType::class, [
                'label' => 'Hide Name',
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle', // optional: if you want to use Bootstrap Toggle
                ],
            ])

            ->add('question')
            ->add('image', FileType::class, array('data_class' => null), [
                'required' => false, [
                    'mapped' => false
                ]
            ])
            ->add('Confirmer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
