<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_combine;
use function array_values;
use function join;
use function Symfony\Component\String\s;

/**
 * @author Manuel Aguirre
 */
class ResourceType extends AbstractType
{
    public function __construct(private RolesProviderInterface $rolesProvider)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profiles', ChoiceType::class, [
            'label' => $options['resource']->getName(),
            'choice_loader' => ChoiceList::lazy($this, function () {
                $choices = array_values($this->rolesProvider->getRoles());

                return array_combine($choices, $choices);
            }),
            'choice_label' => $options['show_profile_label'] ? null : false,
            'multiple' => true,
            'expanded' => true,
        ]);
        $builder->add('all', CheckboxType::class, [
            'label' => $options['show_profile_label'] ? 'All' : false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('resource');
        $resolver->setAllowedTypes('resource', Resource::class);

        $resolver->setDefaults([
            'show_profile_label' => true,
        ]);

        $resolver->setAllowedTypes('show_profile_label', 'bool');

        $resolver->setDefault('label', function (Options $options) {
            return $options['resource']->getName();
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