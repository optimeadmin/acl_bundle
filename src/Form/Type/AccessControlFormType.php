<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Resource\Hierarchy\ResourcesFormatter;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesWithRolesRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_map;
use function dd;

/**
 * @author Manuel Aguirre
 */
class  AccessControlFormType extends AbstractType
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private RolesProviderInterface $rolesProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resources = $this->resourceRepository->allVisible();

        foreach ($resources as $resource) {
            $builder->add($resource->getId(), ResourceType::class, [
                'resource' => $resource,
                'show_role_label' => $options['show_role_label'],
                'property_path' => 'resources[' . $resource->getId() . ']',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_role_label' => true,
            'data_class' => ResourcesWithRolesRequest::class,
        ]);

        $resolver->setAllowedTypes('show_role_label', 'bool');
        $resolver->setDefault('empty_data', function (FormInterface $form){
            return new ResourcesWithRolesRequest([]);
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['roles'] = $this->rolesProvider->getRoles();
    }
}