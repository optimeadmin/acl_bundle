<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\EventListener;

use Optime\Acl\Bundle\Attribute\Resource;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use function get_debug_type;
use function is_iterable;

/**
 * @author Manuel Aguirre
 */
#[AsEventListener(KernelEvents::CONTROLLER_ARGUMENTS, 'onKernelController')]
class ResourceListener
{
    public function __construct(
        private Security $security,
        private bool $enabledAuth,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function onKernelController(ControllerArgumentsEvent $event): void
    {
        if (!$this->enabledAuth) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->attributes->has('_acl_resource')) {
            return;
        }

        if (!is_iterable($resources = $request->attributes->get('_acl_resource'))) {
            if ($this->logger) {
                $this->logger->warning("Se esperaba que los recursos fuesen un iterador", [
                    'file' => __FILE__,
                    'resources_type' => get_debug_type($resources)
                ]);
            }

            return;
        }

        /** @var Resource $resource */
        foreach ($resources as $resource) {
            if (!$this->security->isGranted('resource', $resource->getResource())) {
                $exception = new AccessDeniedException(sprintf(
                    "El usuario no tiene acceso al recurso '%s'.",
                    $resource->getResource(),
                ));
                $exception->setAttributes('resource');
                $exception->setSubject($resource->getResource());

                throw $exception;
            }
        }
    }
}