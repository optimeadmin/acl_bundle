<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Attribute;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Resource extends Security
{
    private string $reference = '';

    public function __construct(private ?string $resource = null)
    {
        parent::__construct("is_granted('resource','" . $resource . "')", null, null);
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

}