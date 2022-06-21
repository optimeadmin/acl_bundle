<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller\Api;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Resource\ResourcesRolesProvider;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesRolesRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesWithRolesRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\UpdateAllResourcesRolesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function array_values;
use function dump;

/**
 * @author Manuel Aguirre
 */
#[Route("/api/resources", name: "optime_acl_api_resources_")]
class ResourcesController extends AbstractController
{
    #[Route("/clean/", name: "clean")]
    public function aclConfig(
        CleanResourcesUseCase $useCase,
    ): Response {
        $useCase->handle();

        return $this->json('success');
    }

    #[Route("/resources-roles/", name: "save_resources_roles", methods: 'put')]
    public function saveResourcesRoles(
        Request $request,
        SerializerInterface $serializer,
        UpdateAllResourcesRolesUseCase $useCase,
        ResourcesRolesProvider $resourcesRolesProvider,
    ): Response {
        $resourcesRequest = $serializer->deserialize(
            $request->getContent(),
            ResourcesWithRolesRequest::class,
            'json'
        );

        $useCase->handle($resourcesRequest);

        return $this->json($resourcesRolesProvider->getAll());
    }
}