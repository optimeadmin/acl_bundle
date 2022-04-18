<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\Hierarchy;

use LogicException;
use Optime\Acl\Bundle\Entity\Resource;

/**
 * @author Manuel Aguirre
 */
final class FormattedResource
{
    private string $name;
    private array $children = [];

    private function __construct(
        private ?Resource $resource,
        private bool $isParent,
    ) {
        if (!$this->isParent) {
            $this->name = $this->resource->getParent();
            $this->children[(string)$this->resource] = $this->resource;
            $this->resource = null;
        } else {
            $this->name = $this->resource->getName();
        }
    }

    public static function fromParent(Resource $resource): self
    {
        return new self($resource, true);
    }

    public static function fromChild(Resource $resource): self
    {
        return new self($resource, false);
    }

    public function setParent(Resource $resource): void
    {
        if (null !== $this->resource) {
            throw new LogicException("Ya se tiene un recurso padre establecido para la clase formateadora");
        }

        $this->resource = $resource;
    }

    public function appendChild(Resource $resource): void
    {
        $this->children[(string)$resource] = $resource;
    }
}