<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Form\Type\AccessControlFormType;
use Optime\Acl\Bundle\Service\Resource\ResourceLoader;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\RequestFactory;
use Optime\Acl\Bundle\Service\Resource\UseCase\UpdateAllResourcesRolesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/configuration")]
class AccessControlController extends AbstractController
{
    #[Route("/", name: "optime_acl_configuration")]
    public function index(
        Request $request,
        RequestFactory $requestFactory,
        UpdateAllResourcesRolesUseCase $useCase
    ): Response {
        $data = $requestFactory->createResourcesWithRoles();
        $form = $this->createForm(AccessControlFormType::class, $data, [
            'show_role_label' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $useCase->handle($data);

            return $this->redirectToRoute('optime_acl_configuration');
        }

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