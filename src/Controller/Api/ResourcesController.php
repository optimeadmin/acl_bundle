<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller\Api;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesConfigRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\UpdateResourcesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function dump;

/**
 * @author Manuel Aguirre
 */
#[Route("/api/resources", name: "optime_acl_api_resources_")]
class ResourcesController extends AbstractController
{
    #[Route("/clean/", name: "clean", methods: "delete")]
    public function clean(
        CleanResourcesUseCase $useCase,
    ): Response {
        $useCase->handle();

        return $this->json('success');
    }

    #[Route("/", name: "get_all", methods: 'get')]
    public function getResources(
        ResourceRepository $repository,
    ): Response {
        return $this->json($repository->allVisible());
    }

    #[Route("/", name: "save", methods: 'put')]
    public function save(
        Request $request,
        ResourceRepository $repository,
        UpdateResourcesUseCase $useCase,
        SerializerInterface $serializer,
    ): Response {
        $data = $serializer->deserialize($request->getContent(), ResourcesConfigRequest::class, 'json');
        $useCase->handle($data);

        return $this->json($repository->allVisible());
    }
}