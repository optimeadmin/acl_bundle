<?php
/**
 * @author Manuel Aguirre
 */
declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use Optime\Acl\Bundle\Service\Resource\ResourceNameGenerator;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function trim;

/**
 * @author Manuel Aguirre
 */
class DirectoryReferencesLoader
{
    private ?ReferenceCollection $loadedCollection;

    public function __construct(
        private array $namespaces,
        private array $excludedResourcesPatterns,
        private RouterInterface $router,
        private ResourceNameGenerator $nameGenerator,
    ) {
    }

    public function getResources(): ReferenceCollection
    {
        return $this->loadedCollection ??= $this->doGetResources();
    }

    private function doGetResources(): ReferenceCollection
    {
        $collection = new ReferenceCollection();
        $routes = $this->router->getRouteCollection();

        $this->loadFromRoutes($collection, $routes);

        return $collection;
    }

    private function filterRoutesByDefinedDirs(RouteCollection $routes): RouteCollection
    {
        return $routes;
    }

    private function loadFromRoutes(ReferenceCollection $resources, RouteCollection $routes): void
    {
        foreach ($routes as $routeName => $route) {
            $this->loadFromRoute($resources, $route, $routeName);
        }
    }

    private function applyNamespace(string $reference): bool
    {
        foreach ($this->namespaces as $prefixNamespace) {
            if (str_starts_with($reference, $prefixNamespace)) {
                return true;
            }
        }

        return false;
    }

    private function loadFromRoute(
        ReferenceCollection $resources,
        Route $route,
        string $routeName
    ): void {
        if (!$route->hasDefault('_controller')) {
            return;
        }

        $reference = $route->getDefault('_controller');

        if (!$this->applyNamespace($reference)) {
            return;
        }

        try {
            $reflection = new ReflectionMethod($reference);
        } catch (ReflectionException) {
            return;
        }

        if ($this->isExcluded($this->getReferenceName($reflection))) {
            return;
        }

        if (false === ($resourceName = $this->nameGenerator->generateFromReference($reflection))) {
            return;
        }

        $resources->add($reference, $resourceName, $routeName, $route->getPath());
    }

    private function getReferenceName(ReflectionMethod $reflection): string
    {
        return sprintf('%s::%s', $reflection->class, $reflection->getName());
    }

    private function isExcluded(string $reference): bool
    {
        foreach ($this->excludedResourcesPatterns as $pattern) {
            if ($this->match($reference, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function match(string $reference, string $pattern): bool
    {
        $pattern = trim($pattern);
        $search = trim($pattern, '*');

        if ($search !== $pattern) {
            $start = str_starts_with($pattern, '*');
            $end = str_ends_with($pattern, '*');

            if ($start and $end) {
                return str_contains($reference, $search);
            }

            if ($start and str_ends_with($reference, $search)) {
                return true;
            }

            if ($end and str_starts_with($reference, $search)) {
                return true;
            }

            return false;
        } else {
            return $search == $reference;
        }
    }
}