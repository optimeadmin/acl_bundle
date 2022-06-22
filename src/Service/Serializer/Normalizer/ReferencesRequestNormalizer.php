<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Serializer\Normalizer;

use Optime\Acl\Bundle\Service\Reference\Loader\ReferenceCollection;
use Optime\Acl\Bundle\Service\Reference\Loader\ReferencesLoader;
use Optime\Acl\Bundle\Service\Reference\UseCase\Request\ReferencesRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_keys;
use function sprintf;

/**
 * @author Manuel Aguirre
 */
class ReferencesRequestNormalizer implements DenormalizerInterface
{
    public function __construct(
        private ReferencesLoader $referencesLoader,
    ) {
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return ReferencesRequest::class === $type;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $request = new ReferencesRequest();
        $references = $this->referencesLoader->getReferences();

        foreach ($data as $item) {
            [$for, $reference] = $this->denormalizeReference($item, $references);

            if ($for === 'applied') {
                $request->appliedReferences[] = $reference;
            } elseif ($for === 'hidden') {
                $request->hiddenReferences[] = $reference;
            }
        }

        return $request;
    }

    private function denormalizeReference(array $item, ReferenceCollection $references): array
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefined(array_keys($item))
            ->setRequired(['name', 'modifiedResourceName', 'selected', 'hidden'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('modifiedResourceName', 'string')
            ->setAllowedTypes('selected', 'bool')
            ->setAllowedTypes('hidden', 'bool');

        [
            'name' => $name,
            'resource' => $resource,
            'selected' => $apply,
            'hidden' => $hidden,
        ] = $resolver->resolve($item);

        if (!$reference = $references->get($name)) {
            throw new UnexpectedValueException(sprintf(
                "No se encontró la referencia '%s'",
                $name,
            ));
        }

        $reference->setModifiedResourceName($resource);
        $type = $hidden ? 'hidden' : ($apply ? 'applied' : null);


        return [$type, $reference];
    }
}