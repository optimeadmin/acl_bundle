<?php
/**
 * @author Manuel Aguirre
 */
declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Roles;

use Countable;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use function count;

/**
 * @author Manuel Aguirre
 */
class AppRolesCounter implements Countable
{
    private int $count = -1;

    public function __construct(private RolesProviderInterface $rolesProvider)
    {
    }

    public function getCount(): int
    {
        if (-1 === $this->count) {
            $this->count = count($this->rolesProvider->getRoles());
        }

        return $this->count;
    }

    public function count(): int
    {
        return $this->getCount();
    }
}