<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Form\Type\AccessControlFormType;
use Optime\Acl\Bundle\Service\Resource\ResourceLoader;
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
        $form = $this->createForm(AccessControlFormType::class, null, [
            'show_profile_label' => false,
        ]);

        return $this->renderForm('@OptimeAcl/access_control/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/resources/load", name: "optime_acl_load_resources")]
    public function loadResources(ResourceLoader $loader): Response
    {
        $loader->process();

        return $this->redirectToRoute('optime_acl_configuration');
    }
}