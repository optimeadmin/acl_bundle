<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use ArrayIterator;
use IteratorAggregate;
use Optime\Acl\Bundle\Entity\ResourceReference;
use function array_filter;
use function array_values;
use function call_user_func;

/**
 * @author Manuel Aguirre
 */
final class ReferenceCollection implements IteratorAggregate
{
    private array $references = [];

    public function __construct(private bool $onlyActives = true)
    {
    }

    /**
     * @return ArrayIterator|LoadedReference[]
     */
    public function getIterator(): ArrayIterator
    {
        if ($this->onlyActives) {
            $references = array_filter($this->references, fn(LoadedReference $r) => $r->isActive());
        } else {
            $references = $this->references;
        }

        return new ArrayIterator(array_values($references));
    }

    public function add(
        string $name,
        string $resource,
        ?string $route = null,
        ?string $routePath = null
    ): void {
        $this->references[$name] ??= new LoadedReference(
            $name,
            $resource,
            $route,
            $routePath
        );
    }

    public function addOrphan(ResourceReference $reference): void
    {
        $referenceName = $reference->getReference();

        $this->references[$referenceName] ??= new LoadedReference(
            $referenceName,
            $reference->getResource()->getName(),
            null,
            null
        );
    }

    public function addCollection(self $collection): void
    {
        /** @var LoadedReference $item */
        foreach ($collection as $item) {
            $this->add(
                $item->getName(),
                $item->getResource(),
                $item->getRoute(),
                $item->getRoutePath(),
            );
        }
    }

    public function get(string $referenceName): ?LoadedReference
    {
        return $this->references[$referenceName] ?? null;
    }

    public function filter(callable $callback, bool $onlyActives = true): self
    {
        $filteredCollection = new self($onlyActives);

        foreach ($this->references as $item) {
            if (call_user_func($callback, $item)) {
                $filteredCollection->references[$item->getName()] = $item;
            }
        }

        return $filteredCollection;
    }
}
