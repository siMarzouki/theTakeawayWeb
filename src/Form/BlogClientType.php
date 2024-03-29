<?php

namespace App\Form;


use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\BlogClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Commentaire;



class BlogClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class)
            ->add('contenu',TextareaType::class)
            ->add('image', FileType::class, array('data_class'=> null,
                'required'=> false))

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogClient::class,
        ]);
    }
}
