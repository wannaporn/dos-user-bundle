<?php

namespace DoS\UserBundle\Doctrine\ORM;

use DoS\UserBundle\Model\UserInterface;
use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;

/**
 * User repository.
 */
class UserRepository extends BaseUserRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();
        $expr = $queryBuilder->expr();

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            /*if (is_numeric($criteria['query']) && $criteria['query'][0] != '0') {
                $number = $criteria['query'];

                if (strlen($number) < 9) {
                    $number = substr('000000000' . $number, -9);
                }

                $queryBuilder
                    ->andWhere('o.number = :number')
                    ->setParameter('number', $number)
                ;
            } else {*/
                $where = $expr->orX(
                    //$expr->like('o.number', ':query'),
                    $expr->like('o.username', ':query'),
                    $expr->like('o.email', ':query'),
                    $expr->like('o.firstName', ':query'),
                    $expr->like('o.lastName', ':query')
                );

            $queryBuilder
                    ->andWhere($where)
                    ->setParameter('query', '%'.$criteria['query'].'%')
                ;
            /*}*/

            unset($criteria['query']);
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
     * @param int $id
     *
     * @return null|UserInterface
     */
    public function findForDetailsPage($id)
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
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
}
