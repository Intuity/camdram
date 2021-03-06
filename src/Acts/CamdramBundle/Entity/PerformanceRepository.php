<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query\Expr;

/**
 * PerformanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PerformanceRepository extends EntityRepository
{
    /**
     * findAuthorizedJoinedToShow
     *
     * Find all authorized performances between two dates, joined to the 
     * corresponding show.
     *
     * @param integer $startDate start date expressed as a Unix timestamp
     * @param integer $endDate emd date expressed as a Unix timestamp
     *
     * @return array of performances
     */
    public function findAuthorizedJoinedToShow($startDate, $endDate)
    {
        $query_res = $this->getEntityManager()->getRepository('ActsCamdramBundle:Performance');
        $query = $query_res->createQueryBuilder('p')
            ->leftJoin('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 'p.show = s.id')
            ->where('p.start_date <= :enddate')
            ->andWhere('p.end_date >= :startdate')
            ->andWhere('s.authorize_id > 0')
            ->setParameters(array(
                'startdate' => date("Y/m/d", $startDate),
                'enddate' => date("Y/m/d", $endDate)
                ))
            ->orderBy('p.time, p.start_date, s.id, p.end_date')
            ->getQuery();

        return $query->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->andX('p.end_date > :start', 'p.end_date < :end'))
            ->orWhere($qb->expr()->andX('p.start_date > :start', 'p.start_date < :end'))
            ->orWhere($qb->expr()->andX('p.start_date < :start', 'p.end_date > :end'))
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $result = $qb->getQuery()->getResult();
        $count = 0;
        foreach ($result as $p) {
            $count += $p->getEndDate()->diff($p->getStartDate())->d + 1;
            if ($p->getExcludeDate()->format('u') > 0) $count--;
        }

        return $count;
    }
}
