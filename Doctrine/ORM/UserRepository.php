<?php

namespace DoS\UserBundle\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use DoS\ResourceBundle\Doctrine\ORM\EntityRepository;
use DoS\UserBundle\Confirmation\ConfirmationSubjectFinderInterface;
use DoS\UserBundle\Model\UserInterface;
use Pagerfanta\PagerfantaInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * User repository.
 */
class UserRepository extends EntityRepository implements ConfirmationSubjectFinderInterface, UserRepositoryInterface
{
    /**
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            $queryBuilder
                ->leftJoin($this->getAlias().'.customer', 'customer')
                ->where('customer.emailCanonical LIKE :query')
                ->orWhere('customer.firstName LIKE :query')
                ->orWhere('customer.lastName LIKE :query')
                ->orWhere($this->getAlias().'.username LIKE :query')
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }

        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere('o.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the user data for the details page.
     *
     * @param integer $id
     *
     * @return null|UserInterface
     */
    public function findForDetailsPage($id)
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->leftJoin($this->getAlias().'.customer', 'customer')
            ->addSelect('customer')
            ->where($queryBuilder->expr()->eq($this->getAlias().'.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->_em->getFilters()->enable('softdeleteable');

        return $result;
    }

    /**
     * @param \DateTime   $from
     * @param \DateTime   $to
     * @param null|string $status
     *
     * @return mixed
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to);

        if (null !== $status) {
            $queryBuilder
                ->andWhere('o.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param array $configuration
     *
     * @return array
     */
    public function getRegistrationStatistic(array $configuration = array())
    {
        $groupBy = '';

        foreach ($configuration['groupBy'] as $groupByArray) {
            $groupBy = $groupByArray.'(date)'.' '.$groupBy;
        }

        $groupBy = substr($groupBy, 0, -1);
        $groupBy = str_replace(' ', ', ', $groupBy);

        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('DATE(u.created_at) as date', ' count(u.id) as user_total')
            ->from('sylius_user', 'u')
            ->where($queryBuilder->expr()->gte('u.created_at', ':from'))
            ->andWhere($queryBuilder->expr()->lte('u.created_at', ':to'))
            ->setParameter('from', $configuration['start']->format('Y-m-d H:i:s'))
            ->setParameter('to', $configuration['end']->format('Y-m-d H:i:s'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

    /**
     * @param string $email
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function findOneByEmail($email)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->leftJoin($this->getAlias().'.customer', 'customer')
            ->andWhere($queryBuilder->expr()->eq('customer.emailCanonical', ':email'))
            ->setParameter('email', $email)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
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

    /**
     * @param UserInterface|null $user
     *
     * @return UserInterface|null
     */
    public function findUserByUser(UserInterface $user = null)
    {
        return $user;
    }

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
}
