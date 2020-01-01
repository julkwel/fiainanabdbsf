<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'label' => 'prenom',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'birthDate',
                DateTimeType::class,
                [
                    'label' => 'Date de naissance',
                    'html5' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy HH:mm',
                    'attr' => [
                        'class' => 'datetime-picker'
                    ]
                ]
            )
            ->add(
                'username',
                EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mots de passe'],
                    'second_options' => ['label' => 'Confirmer votre mots de passe'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}