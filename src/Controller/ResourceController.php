<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Form\Type\Config\ResourcesConfigType;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\UpdateResourcesUseCase;
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
    public function __construct(
        private CleanResourcesUseCase $cleanResourcesUseCase,
    ) {
    }

    #[Route("/", name: "optime_acl_resources_list")]
    public function list(
        Request $request,
        ResourceRepository $repository,
        UpdateResourcesUseCase $useCase,
    ): Response {
        $resources = $repository->allVisible();
        $form = $this->createForm(ResourcesConfigType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $useCase->handle($form);
            $this->cleanResourcesUseCase->handle();

            $this->addFlash('success', 'Data saved successfully!');

            return $this->redirectToRoute('optime_acl_resources_list');
        }

        return $this->renderForm('@OptimeAcl/resource/list.html.twig', [
            'resources' => $resources,
            'form' => $form,
        ]);
    }

    #[Route("/clean/", name: "optime_acl_resources_clean")]
    public function clean(Request $request): Response
    {
        $this->cleanResourcesUseCase->handle();
        $this->addFlash('success', 'Data saved successfully!');

        return $this->redirect($request->headers->get(
            'referer',
            $this->generateUrl('optime_acl_resources_list')
        ));
    }
}