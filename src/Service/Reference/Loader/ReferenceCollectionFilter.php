<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use function dd;

/**
 * @author Manuel Aguirre
 */
class ReferenceCollectionFilter
{
    public function __construct(private ResourceReferenceRepository $referenceRepository)
    {
    }

    public function news(ReferenceCollection $collection): ReferenceCollection
    {
        return $collection->filter(function (LoadedReference $reference) {
            return !$this->referenceRepository->referenceExists($reference->getName());
        });
    }

    public function existent(ReferenceCollection $collection): ReferenceCollection
    {
        return $collection->filter(function (LoadedReference $reference) {
            return $this->referenceRepository->referenceExists($reference->getName());
        });
    }

    public function hidden(ReferenceCollection $collection): ReferenceCollection
    {
        return $collection->filter(function (LoadedReference $reference) {
            return $this->referenceRepository->referenceExists($reference->getName()) && !$reference->isActive();
        }, false);
    }
}