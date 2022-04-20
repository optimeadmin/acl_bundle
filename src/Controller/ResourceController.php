<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/resources")]
class ResourceController extends AbstractController
{
    #[Route("/", name: "optime_acl_resources_list")]
    public function list(
        Request $request,
        ResourceRepository $repository,
    ): Response {
        $resources = $repository->allVisible();

        return $this->render('@OptimeAcl/resource/list.html.twig', [
            'resources' => $resources,
        ]);
    }

    #[Route("/clean/", name: "optime_acl_resources_clean")]
    public function clean(Request $request, CleanResourcesUseCase $useCase): Response
    {
        $useCase->handle();
        $this->addFlash('success', 'Data saved successfully!');

        return $this->redirect($request->headers->get(
            'referer',
            $this->generateUrl('optime_acl_resources_list')
        ));
    }
}