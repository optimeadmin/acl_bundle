<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\EventListener;

use Optime\Acl\Bundle\Attribute\Resource;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use function sprintf;

/**
 * @author Manuel Aguirre
 */
#[AsEventListener(KernelEvents::CONTROLLER_ARGUMENTS, 'onKernelController')]
class ResourceListener
{
    public function __construct(
        private Security $security,
        private bool $enabledAuth,
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

        $this->checkResource($request->attributes->get('_acl_resource'));
    }

    private function checkResource(Resource $resource): void
    {
        if (!$this->security->isGranted('resource', $resource->getResource())) {
            $exception = new AccessDeniedException(sprintf(
                "El usuario no tiene acceso al recurso: '%s'.",
                $resource->getResource(),
            ));
            $exception->setAttributes('resource');
            $exception->setSubject($resource->getResource());

            throw $exception;
        }
    }
}