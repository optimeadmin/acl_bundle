<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\EventListener;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\FirewallMapInterface;
use function dd;
use function dump;
use function sprintf;

/**
 * @author Manuel Aguirre
 */
#[AsEventListener(KernelEvents::CONTROLLER, 'onKernelController')]
class SecurityListener
{
    public function __construct(
        private Security $security,
        private ResourceReferenceRepository $referenceRepository,
        private FirewallMapInterface $firewallMap,
        private bool $enabledAuth,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$this->enabledAuth) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->attributes->has('_controller')) {
            return;
        }

        if ($this->firewallMap instanceof FirewallMap) {
            if (!$this->firewallMap->getFirewallConfig($request)?->isSecurityEnabled() ?? true) {
                return;
            }
        }

        if ($event->getController() instanceof ErrorController) {
            return;
        }

        $this->checkAccess($request->attributes->get('_controller'));
    }

    private function checkAccess(string $reference): void
    {
        if (!$this->security->isGranted('resource_reference', $reference)) {
            $referenceEntity = $this->referenceRepository->byName($reference);

            if ($referenceEntity) {
                $exception = new AccessDeniedException(sprintf(
                    "El usuario no tiene acceso al recurso: %s",
                    $referenceEntity->getResource()->getName(),
                ));
            } else {
                $exception = new AccessDeniedException("El usuario no tiene acceso a la url actual");
            }

            $exception->setAttributes('resource_reference');
            $exception->setSubject($reference);

            throw $exception;
        }
    }
}