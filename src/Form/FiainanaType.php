<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Form;

use App\Entity\Fiainana;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FiainanaType.
 */
class FiainanaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'publicationDate',
                DateTimeType::class,
                [
                    'label' => 'Date de publication',
                    'html5' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy HH:mm',
                    'attr' => [
                        'class' => 'datetime-picker datetimepicker-input',
                        'data-toggle' => 'datetimepicker',
                        'data-target' => '#fiainana_publicationDate',
                        'autocomplete'=>'off'
                    ]
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'required' => false
                ]
            )
            ->add(
                'avatar',
                FileType::class,
                [
                    'label' => 'Photo',
                    'required' => false,
                    'mapped' => false
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Fiainana::class]);
    }
}
