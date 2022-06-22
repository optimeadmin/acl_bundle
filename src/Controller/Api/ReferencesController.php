<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller\Api;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Reference\Loader\ReferenceCollectionFilter;
use Optime\Acl\Bundle\Service\Reference\Loader\ReferencesLoader;
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
#[Route("/api/references", name: "optime_acl_api_references_")]
class ReferencesController extends AbstractController
{
    #[Route("/", name: "get_all", methods: 'get')]
    public function getAll(
        ReferencesLoader $loader,
        ReferenceCollectionFilter $collectionFilter,
    ): Response {
        $references = $loader->getReferences();

        return $this->json([
            'existent' => $collectionFilter->existent($references),
            'news' => $collectionFilter->news($references),
            'hidden' => $collectionFilter->hidden($references),
        ]);
    }

    #[Route("/", name: "save", methods: 'put')]
    public function save(
        Request $request,
        ResourceRepository $repository,
        UpdateResourcesUseCase $useCase,
        SerializerInterface $serializer,
    ): Response {
//        $data = $serializer->deserialize($request->getContent(), ResourcesConfigRequest::class, 'json');
//        $useCase->handleApi($data);

        return $this->json($repository->allVisible());
    }
}