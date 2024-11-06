<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Comment1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        //    ->add('article_id')
        //    ->add('user_id')
        //    ->add('comment_date_created', null, [
        //       'widget' => 'single_text',
        //    ])
        //    ->add('comment_username')
            ->add('comment_text')
            ->add('visible')
        //    ->add('article', EntityType::class, [
        //        'class' => Article::class,
        //        'choice_label' => 'id',
        //    ])
        //    ->add('user', EntityType::class, [
        //        'class' => User::class,
        //        'choice_label' => 'id',
        //    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
