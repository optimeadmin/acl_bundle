<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Resource\Hierarchy\ResourcesFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Manuel Aguirre
 */
class AccessControlFormType extends AbstractType
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private RolesProviderInterface $rolesProvider,
        private ResourcesFormatter $resourcesFormatter,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $resources = $this->resourcesFormatter->format($this->resourceRepository->allVisible());
        $resources = $this->resourceRepository->allVisible();

        foreach ($resources as $resource) {
            $builder->add($resource->getId(), ResourceType::class, [
                'resource' => $resource,
                'show_profile_label' => $options['show_profile_label']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_profile_label' => true,
        ]);

        $resolver->setAllowedTypes('show_profile_label', 'bool');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['profiles'] = $this->rolesProvider->getRoles();
    }
}