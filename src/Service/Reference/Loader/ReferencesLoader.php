<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;

/**
 * @author Manuel Aguirre
 */
class ReferencesLoader
{
    private ?ReferenceCollection $loadedCollection;

    public function __construct(
        private DirectoryReferencesLoader $referencesLoader,
        private ResourceReferenceRepository $repository,
    ) {
    }

    public function getResources(): ReferenceCollection
    {
        return $this->loadedCollection ??= $this->doGetResources();
    }

    private function doGetResources(): ReferenceCollection
    {
        $collection = $this->referencesLoader->getResources();
        $databaseReferences = $this->repository->findAll();

        foreach ($databaseReferences as $reference) {
            if ($loadedReference = $collection->get($reference->getReference())) {
                $loadedReference->attachReference($reference);
            } else {
                $collection->addOrphan($reference);
            }
        }

        return $collection;
    }
}