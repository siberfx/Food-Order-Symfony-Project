<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Restaurant;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class,[
                'class' =>Category::class,
                'choice_label' => 'title',
            ])


            ->add('title',TextType::class,['label' => 'Restaurant Name'])
            ->add('keywords')
            ->add('description')

            ->add('image', FileType::class,[
                    'label'=> 'Restaurant Main Image',
                    'mapped'=>false,
                    'required' => false,
        'constraints' =>[
            new File([
                'maxSize' =>'1024k',
                'mimeTypes' =>[
                    'image/*', //allimage type
                ],
                'mimeTypesMessage' => 'Please upload a valid Image File'
            ])
    ],
                ])

            ->add('address')
            ->add('phone')
            ->add('email')
            ->add('country',ChoiceType::class,[
                'choices'=>[
                    'Turkiye'=>'Turkiye',
                    'Spain'=>'Spain',
                    'Greece'=>'Greece',
                    'Russia'=>'Russia',
                    'France'=>'Frans'],
            ])
            ->add('city',ChoiceType::class,[
        'choices'=>[
            'Ankara'=>'Ankara',
            'Istanbul'=>'Istanbul',
            'Antalya'=>'Antalya',
            'Paris'=>'Paris',
            'Lyon'=>'Lyon'],
    ])

            ->add('location')
            ->add('detail',CKEditorType::class, array(
                'config'=>array(
                    'uiColor'=>'#ffffff',
                    //...
                ),
            ))

            ->add('status',ChoiceType::class,[
                'choices'=>[
                    'True'=>'True',
                    'False'=>'False'],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
            'csrf_protection'=> false,

        ]);
    }
}
