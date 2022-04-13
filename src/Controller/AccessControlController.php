<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/configuration")]
class AccessControlController extends AbstractController
{
    #[Route("/", name: "optime_acl_configuration")]
    public function index(): Response
    {
        return $this->render('@OptimeAcl/layout.html.twig');
    }
}