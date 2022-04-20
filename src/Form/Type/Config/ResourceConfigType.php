<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type\Config;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function strlen;

/**
 * @author Manuel Aguirre
 */
class ResourceConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('apply', CheckboxType::class, [
            'mapped' => false,
            'label' => false,
        ]);
        $builder->add('name', TextType::class, [
            'empty_data' => '',
        ]);
        $builder->add('description', TextareaType::class, [
            'empty_data' => '',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Resource::class);
        $resolver->setRequired('data');
        $resolver->setAllowedTypes('data', Resource::class);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['resource'] = $options['data'];
    }
}