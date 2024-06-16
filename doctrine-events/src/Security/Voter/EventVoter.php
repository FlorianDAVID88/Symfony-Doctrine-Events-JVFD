<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    public const ADD = 'POST_ADD';
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ADD, self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ADD:
                return $user instanceof UserInterface;

            case self::EDIT:
            case self::DELETE:
                if (!$user instanceof UserInterface) {
                    return false;
                }

                $hasUserRole = in_array('ROLE_USER', $user->getRoles());
                if ($hasUserRole && $subject instanceof Event) {
                    return $subject->getCreator() === $user;
                }
                return false;

            case self::VIEW:
                if ($subject instanceof Event) {
                    return $subject->isPublic() || $user instanceof UserInterface;
                }
                return false;
        }

        return false;
    }
}
