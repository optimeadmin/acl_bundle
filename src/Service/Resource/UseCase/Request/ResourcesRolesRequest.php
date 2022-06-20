<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase\Request;

/**
 * @author Manuel Aguirre
 */
class ResourcesRolesRequest
{
    public function __construct(public array $resources)
    {
    }
}