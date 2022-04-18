<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\Hierarchy;

use Optime\Acl\Bundle\Entity\Resource;
use function dd;

/**
 * @author Manuel Aguirre
 */
class ResourcesFormatter
{
    /**
     * @param iterable|Resource[] $resources
     */
    public function format(iterable $resources)
    {
//        return $resources;
        $format = [];

        foreach ($resources as $resource) {
            if ($resource->hasParent()) {
                $index = $resource->getParent();
                if (!isset($format[$index])) {
                    $format[$index] = FormattedResource::fromChild($resource);
                } else {
                    $format[$index]->appendChild($resource);
                }
            } else {
                $index = (string)$resource;
                if (!isset($format[$index])) {
                    $format[$index] = FormattedResource::fromParent($resource);
                } else {
                    $format[$index]->setParent($resource);
                }
            }
        }

        dd($format);
    }
}