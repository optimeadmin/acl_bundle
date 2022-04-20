<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Service\Reference\Loader\ReferenceCollectionFilter;
use Optime\Acl\Bundle\Service\Reference\Loader\ReferencesLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function dd;
use function dump;

/**
 * @author Manuel Aguirre
 */
class ResourcesConfigType extends AbstractType
{
    private static int $nameSuffix = 0;

    public function __construct(
        private ReferencesLoader $loader,
        private ReferenceCollectionFilter $collectionFilter
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resources = $this->loader->getResources();

        if ($options['hidden']) {
            $resources = $this->collectionFilter->hidden($resources);
        } elseif ($options['persisted']) {
            $resources = $this->collectionFilter->existent($resources);
        } else {
            $resources = $this->collectionFilter->news($resources);
        }

        $form = $builder->create('references', FormType::class);

        /** @var \Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference $resource */
        foreach ($resources as $resource) {
            $form->add($resource->getIdentifier(), ResourceConfigType::class, [
                'data' => $resource,
                'edit' => $options['persisted'],
            ]);
        }

        $builder->add($form);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('persisted', true);
        $resolver->setAllowedTypes('persisted', 'bool');
        $resolver->setDefault('hidden', false);
        $resolver->setAllowedTypes('hidden', 'bool');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['only_hidden'] = $options['hidden'];
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix() . '_' . self::$nameSuffix++;
    }
}