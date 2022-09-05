<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'article'
                ]
            ])
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'Description',
                    'attr' => [
                        'placeholder' => 'Description de l\'article'
                    ]
                ]
            )
            ->add(
                'picture',
                FileType::class,
                [
                    'label' => 'Image',
                    'attr' => [
                        'placeholder' => 'Ilustration de l\'article'
                    ]
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu',
                    'attr' => [
                        'placeholder' => 'Contenu de l\'article',
                        'rows' => 20
                    ]
                ]
            )
            ->add('Ajouter', SubmitType::class, [
                'label' => 'Ajouter un article',
                'attr' => [
                    'class' => 'btn btn-dark m-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
