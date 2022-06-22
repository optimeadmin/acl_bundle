<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Serializer\Normalizer;

use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\RequestFactory;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesConfigRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_keys;
use function sprintf;

/**
 * @author Manuel Aguirre
 */
class ResourcesConfigRequestNormalizer implements DenormalizerInterface
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private RequestFactory $requestFactory,
    ) {
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return ResourcesConfigRequest::class === $type;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $request = new ResourcesConfigRequest();

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
            ->setRequired(['id', 'name', 'description'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('description', 'string');

        [
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ] = $resolver->resolve($item);

        if ($id && !$resource = $this->resourceRepository->find($id)) {
            throw new UnexpectedValueException(sprintf(
                "No se encontró un recurso para el id '%s'",
                $id,
            ));
        } else {
            $resource = new Resource($name, null, true);
        }

        $resourceRequest = new ResourceRequest($resource);
        $resourceRequest->name = (string)$name;
        $resourceRequest->description = $description;

        return $resourceRequest;
    }
}