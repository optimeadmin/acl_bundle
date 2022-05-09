<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type\Config;

use Optime\Acl\Bundle\Entity\Resource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Manuel Aguirre
 */
class CreateResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        $resolver->setAllowedTypes('data', null);
        $resolver->setDefault('empty_data', function (FormInterface $form) {
            return new Resource(
                (string)$form->get('name')->getData(),
                null,
                true,
            );
        });
        $resolver->setDefault('validation_groups', ['Default', 'create']);
    }
}