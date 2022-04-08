<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Attribute;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Resource extends Security
{
    public function __construct(private string $resource)
    {
        parent::__construct("is_granted('resource','" . $resource . "')", null, null);
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }


}