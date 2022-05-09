<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use function dirname;

class OptimeAclBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}