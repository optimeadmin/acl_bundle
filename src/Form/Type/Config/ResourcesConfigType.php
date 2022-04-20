<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\Type\Config;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Manuel Aguirre
 */
class ResourcesConfigType extends AbstractType
{
    public function __construct(private ResourceRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resources = $this->repository->allVisible();

        $form = $builder->create('resources', FormType::class);

        foreach ($resources as $resource) {
            $form->add($resource->getId(), ResourceConfigType::class, [
                'data' => $resource,
            ]);
        }

        $builder->add($form);
    }
}