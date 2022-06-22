<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\UseCase\Request;

use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;

/**
 * @author Manuel Aguirre
 */
class ReferencesRequest
{
    /**
     * @var array|LoadedReference[]
     */
    public array $appliedReferences = [];
    /**
     * @var array|LoadedReference[]
     */
    public array $hiddenReferences = [];
}