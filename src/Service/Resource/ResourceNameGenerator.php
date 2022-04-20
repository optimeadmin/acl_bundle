<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource;

use ReflectionException;
use ReflectionMethod;
use function is_string;
use function Symfony\Component\String\u;

/**
 * @author Manuel Aguirre
 */
class ResourceNameGenerator
{
    public function generateFromReference(string|ReflectionMethod $reference): string|false
    {
        if (is_string($reference)) {
            try {
                $reference = new ReflectionMethod($reference);
            } catch (ReflectionException) {
                return false;
            }
        }

        $class = $reference->getDeclaringClass()->getShortName();
        $method = $reference->getName();

        return sprintf(
            '%s %s',
            u($class)->replaceMatches('/^(.+)Controller$/', '$1')->snake(),
            u($method)->replaceMatches('/^(.+)Action/', '$1')->snake(),
        );
    }
}