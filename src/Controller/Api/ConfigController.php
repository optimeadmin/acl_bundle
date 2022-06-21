<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller\Api;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Resource\ResourcesRolesProvider;
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
#[Route("/api", name: "optime_acl_api_")]
class ConfigController extends AbstractController
{
    #[Route("/config/", name: "config")]
    public function aclConfig(
        RolesProviderInterface $rolesProvider,
        ResourcesRolesProvider $resourcesRolesProvider,
    ): Response {
        return $this->json([
            'roles' => array_values($rolesProvider->getRoles()),
            'resources' => $resourcesRolesProvider->getAll(),
        ]);
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