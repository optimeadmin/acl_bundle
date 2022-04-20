<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        $builder->add('hide', CheckboxType::class, [
            'mapped' => false,
            'label' => false,
        ]);
        $builder->add('resource', TextType::class, [
            'property_path' => 'modifiedResourceName',
            'empty_data' => '',
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            if (!$form->get('apply')->getData()) {
                return;
            }
            $resource = $form->get('resource')->getData();
            if (strlen($resource) && LoadedReference::HIDDEN !== $resource) {
                return;
            }

            $form->get('resource')->addError(new FormError("Invalid resource name"));
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', LoadedReference::class);
        $resolver->setRequired('data');
        $resolver->setAllowedTypes('data', LoadedReference::class);
        $resolver->setRequired('edit');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var LoadedReference $reference */
        $reference = $options['data'];
        $view->vars['reference'] = $reference;
        $view->vars['is_edit'] = $options['edit'];
    }
}