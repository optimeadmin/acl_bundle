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
    public ?string $description;
    public array $roles = [];
    public bool $selectedAll = false;

    public function __construct(private Resource $resource)
    {
        $this->name = $this->resource->getName();
        $this->description = $this->resource->getDescription();
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }
}