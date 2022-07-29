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
    #[Route("/{page?null<.*>}", name: "optime_acl_configuration", priority: -10)]
    public function index(): Response
    {
        $headerTitle = $this->container->get('parameter_bag')->get('optime_acl.header.title');
        $headerPath = $this->container->get('parameter_bag')->get('optime_acl.header.path');

        return $this->renderForm('@OptimeAcl/access_control/home.html.twig', [
            'header_title' => $headerTitle,
            'header_path' => $headerPath,
        ]);
    }
}