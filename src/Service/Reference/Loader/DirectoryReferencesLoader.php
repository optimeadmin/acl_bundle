<?php
/**
 * @author Manuel Aguirre
 */
declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use Optime\Acl\Bundle\Service\Resource\ResourceNameGenerator;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Manuel Aguirre
 */
class DirectoryReferencesLoader
{
    private ?ReferenceCollection $loadedCollection;

    public function __construct(
        private array $dirs,
        private LoaderInterface $loader,
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

        foreach ($this->dirs as $dir) {
            /** @var RouteCollection $routes */
            $routes = $this->loader->load($dir);

            $this->loadFromRoutes($collection, $routes);
        }

        return $collection;
    }

    private function loadFromRoutes(ReferenceCollection $resources, RouteCollection $routes): void
    {
        foreach ($routes as $routeName => $route) {
            $this->loadFromRoute($resources, $route, $routeName);
        }
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

        try {
            $reflection = new ReflectionMethod($reference);
        } catch (ReflectionException) {
            return;
        }

        if (false === ($resourceName = $this->nameGenerator->generateFromReference($reflection))) {
            return;
        }

        $resources->add($reference, $resourceName, $routeName, $route->getPath());
    }
}