<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Serializer\Normalizer;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\RequestFactory;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesWithRolesRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_keys;
use function sprintf;

/**
 * @author Manuel Aguirre
 */
class ResourcesWithRolesRequestNormalizer implements DenormalizerInterface
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private RequestFactory $requestFactory,
    ) {
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return ResourcesWithRolesRequest::class === $type;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $request = new ResourcesWithRolesRequest([]);

        foreach ($data as $item) {
            $request->resources[] = $this->denormalizeResource($item);
        }

        return $request;
    }

    private function denormalizeResource(array $item): ResourceRequest
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefined(array_keys($item))
            ->setRequired(['name', 'roles'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('roles', 'array');

        ['name' => $name, 'roles' => $roles] = $resolver->resolve($item);

        if (!$resource = $this->resourceRepository->findOneByName($name)) {
            throw new UnexpectedValueException(sprintf(
                "No se encontró un recurso para el nombre '%s'",
                $name,
            ));
        }

        $resourceRequest = $this->requestFactory->createResourceRequest($resource);
        $resourceRequest->roles = $roles;

        return $resourceRequest;
    }
}