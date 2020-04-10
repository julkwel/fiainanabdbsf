<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Form;

use App\Entity\Tosika;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TosikaType.
 */
class TosikaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     *
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Tosika::class]);
    }
}
