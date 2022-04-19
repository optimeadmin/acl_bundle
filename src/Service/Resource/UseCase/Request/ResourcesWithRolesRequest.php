<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase\Request;

/**
 * @author Manuel Aguirre
 */
class ResourcesWithRolesRequest
{
    /**
     * @param array|ResourceRequest[] $resources
     */
    public function __construct(
        public array $resources
    ) {
    }
}