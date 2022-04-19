<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Form\DataMapper;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Optime\Acl\Bundle\Service\Roles\AppRolesCounter;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use function count;
use function get_debug_type;
use function iterator_to_array;

/**
 * @author Manuel Aguirre
 */
class ResourceDataMapper implements DataMapperInterface
{
    public function __construct(
        private Resource $resource,
        private AppRolesCounter $rolesCounter,
    ) {
    }

    public static function createRequest(self $dataMapper): ResourceRequest
    {
        return new ResourceRequest($dataMapper->resource);
    }

    public function mapDataToForms($viewData, \Traversable $forms)
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof ResourceRequest) {
            throw new InvalidArgumentException(sprintf(
                "Se esperaba data de tipo '%s', pero llegó '%s'",
                ResourceRequest::class,
                get_debug_type($viewData),
            ));
        }

        $forms = iterator_to_array($forms);

        $forms['roles']->setData($viewData->roles);
        $forms['all']->setData(count($this->rolesCounter) === count($viewData->roles));
    }

    public function mapFormsToData(\Traversable $forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        $viewData->selectedAll = $forms['all']->getData();
        $viewData->roles = $forms['roles']->getData();
    }
}