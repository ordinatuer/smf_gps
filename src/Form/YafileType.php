<?php
namespace App\Form;

use App\Entity\Yafile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YafileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
                ])
            ->add('file', FileType::class, [
                'label' => 'CSV Файл, разделитель ","',
                'mapped' => false,
                'required' => true,
                'constraints' => new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'text/csv',
                        //'application/gpx+xml',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid CSV document',
                ]),
            ])
            ->add('Load', SubmitType::class, ['label' => 'Загрузить!']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Yafile::class,
        ]);
    }
}