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

    public function getReferences(): ReferenceCollection
    {
        return $this->loadedCollection ??= $this->doGetReferences();
    }

    private function doGetReferences(): ReferenceCollection
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