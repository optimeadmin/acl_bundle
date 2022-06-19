<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
class HomeController extends AbstractController
{
    #[Route("/react/{page<.*>}", name: "optime_acl_homepage")]
    public function index(): Response
    {
        return $this->renderForm('@OptimeAcl/access_control/home.html.twig', [
        ]);
    }
}