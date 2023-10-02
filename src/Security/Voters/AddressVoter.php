<?php
namespace App\Security\Voters;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\Address;
use App\Entity\User;
use App\Entity\Yafile;

class AddressVoter extends Voter
{
    const SHOW = 'show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::SHOW])) {
            return false;
        }

        if (! $subject instanceof Address) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::SHOW => $this->canShow($subject, $user),
            default => throw new \LogicException('No code')
        };
        return false;
    }

    private function canShow($address, $user):bool
    {
        if (!$address->getFile()) {
            return false;
        }
        
        return $user === $address->getFile()->getUser();
    }
}