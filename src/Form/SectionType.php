<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section_title')
    //        ->add('section_slug')
            ->add('section_detail')
    /*        ->add('articles', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
    */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
