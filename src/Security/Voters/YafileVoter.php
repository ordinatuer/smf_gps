<?php
namespace App\Security\Voters;

use LogicException;
use App\Entity\User;
use App\Entity\Yafile;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class YafileVoter extends Voter
{
    const FILE = 'file';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (! in_array($attribute, [self::FILE])) {
            return false;
        }

        if (! $subject instanceof Yafile) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if(! $user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::FILE => $this->canFile($subject, $user),
            default => throw new LogicException(),
        };
    }

    private function canFile($yafile, $user)
    {
        return $yafile->getYuser() === $user;
    }
}