<?php

namespace DoS\UserBundle\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use DoS\UserBundle\Confirmation\ConfirmationSubjectFinderInterface;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;

/**
 * User repository.
 */
class UserRepository extends BaseUserRepository implements ConfirmationSubjectFinderInterface
{
    /**
     * @param $propertyPath
     * @param $value
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findConfirmationSubject($propertyPath, $value)
    {
        $queryBuilder = $this->getQueryBuilder();
        $paths = explode('.', $propertyPath);

        // support only 1 step join
        if (count($paths) > 1) {
            $queryBuilder
                ->join($this->getPropertyName($paths[0]), '_p1')
                ->where('_p1.' . $paths[1] . ' = :value')
                ->setParameter('value', $value)
            ;
        } else {
            $queryBuilder
                ->where($this->getPropertyName($propertyPath) . ' = :value')
                ->setParameter('value', $value)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function loadUserByAuthorizationRoles(array $roles)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->join('o.authorizationRoles', 'a')
            ->andWhere($queryBuilder->expr()->in('a.code', ':roles'))
            ->setParameter('roles', $roles)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
