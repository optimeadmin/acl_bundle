<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase\Request;

use Optime\Acl\Bundle\Entity\Resource;

/**
 * @author Manuel Aguirre
 */
class ResourceRequest
{
    public string $name;
    public array $roles = [];
    public bool $selectedAll = false;

    public function __construct(private Resource $resource)
    {
        $this->name = $this->resource->getName();
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }
}