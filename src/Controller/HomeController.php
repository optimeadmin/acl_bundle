<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
class HomeController extends AbstractController
{
    #[Route("/", name: "optime_acl_home")]
    public function redirectToAccessControl(): RedirectResponse
    {
        return $this->redirectToRoute('optime_acl_configuration');
    }
}