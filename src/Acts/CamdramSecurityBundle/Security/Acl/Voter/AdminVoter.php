<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramBundle\Entity\Entity;

/**
 * Grants access if
 */
class AdminVoter implements VoterInterface
{
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($token->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_ADMIN') return self::ACCESS_GRANTED;
        }

        return self::ACCESS_ABSTAIN;
    }

    /**
     * You can override this method when writing a voter for a specific domain
     * class.
     *
     * @param string $class The class name
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return true;
    }
}
