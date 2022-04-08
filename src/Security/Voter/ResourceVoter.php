<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter
{

    protected function supports(string $attribute, $subject)
    {
        return 'resource' === strtolower($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        dump($subject);
        return true;
    }
}