<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Form\DataMapper\ResourceDataMapper;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Optime\Acl\Bundle\Service\Roles\AppRolesCounter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function count;
use function Symfony\Component\String\s;

/**
 * @author Manuel Aguirre
 */
class ResourceType extends AbstractType
{
    public function __construct(
        private RolesProviderInterface $rolesProvider,
        protected AppRolesCounter $rolesCounter,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('roles', ChoiceType::class, [
            'label' => $options['resource']->getName(),
            'choice_loader' => ChoiceList::lazy($this, function () {
                return $this->rolesProvider->getRoles();
            }),
            'choice_value' => 'role',
            'choice_label' => $options['show_role_label'] ? 'label' : false,
            'multiple' => true,
            'expanded' => true,
        ]);
        $builder->add('all', CheckboxType::class, [
            'label' => $options['show_role_label'] ? 'All' : false,
            'property_path' => 'selectedAll',
        ]);

        $builder->setDataMapper(new ResourceDataMapper(
            $options['resource'], $this->rolesCounter
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ResourceRequest::class);
        $resolver->setRequired('resource');
        $resolver->setAllowedTypes('resource', Resource::class);

        $resolver->setDefaults([
            'show_role_label' => true,
        ]);

        $resolver->setAllowedTypes('show_role_label', 'bool');

        $resolver->setDefault('label', function (Options $options) {
            return $options['resource']->getName();
        });
        $resolver->setDefault('empty_data', function (FormInterface $form) {
            return ResourceDataMapper::createRequest($form->getConfig()->getDataMapper());
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var Resource $resource */
        $resource = $options['resource'];
        $view->vars['current_resource'] = s($resource->getName())->replace(' ', '-');
        $view->vars['parent_resource'] = null;
        $view->vars['item_level'] = $resource->getLevel();

        if ($resource->hasParent()) {
            $view->vars['parent_resource'] = s($resource->getParent())->replace(' ', '-');
        }
    }

    public function getBlockPrefix()
    {
        return 'acl_resource';
    }
}