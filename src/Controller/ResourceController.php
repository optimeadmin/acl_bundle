<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Form\Type\Config\CreateResourceType;
use Optime\Acl\Bundle\Form\Type\Config\ResourcesConfigType;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\CreateResourceUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\Exception\DeleteResourceException;
use Optime\Acl\Bundle\Service\Resource\UseCase\RemoveResourceUseCase;
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
        UpdateResourcesUseCase $useCase,
    ): Response {
        $form = $this->createForm(ResourcesConfigType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $useCase->handle($form);
            $this->cleanResourcesUseCase->handle();

            $this->addFlash('success', 'Data saved successfully!');

            return $this->redirectToRoute('optime_acl_resources_list');
        }

        return $this->renderForm('@OptimeAcl/resource/list.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/create", name: "optime_acl_resource_create")]
    public function create(
        Request $request,
        CreateResourceUseCase $useCase,
    ): Response {
        $form = $this->createForm(CreateResourceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $useCase->handle($form->getData());
            $this->addFlash('success', 'Data saved successfully!');

            return $this->redirectToRoute('optime_acl_resources_list');
        }

        return $this->renderForm('@OptimeAcl/resource/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/remote/{id}/", name: "optime_acl_resource_remove")]
    public function remove(
        Resource $resource,
        RemoveResourceUseCase $useCase,
    ): Response {
        try {
            $useCase->handle($resource);
            $this->addFlash('success', 'Data removed successfully!');
        } catch (DeleteResourceException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('optime_acl_resources_list');
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