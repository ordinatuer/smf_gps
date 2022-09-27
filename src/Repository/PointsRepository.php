<?php

namespace App\Repository;

use App\Entity\Points;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

use App\geo\Objects\Bounds;


/**
 * @method Points|null find($id, $lockMode = null, $lockVersion = null)
 * @method Points|null findOneBy(array $criteria, array $orderBy = null)
 * @method Points[]    findAll()
 * @method Points[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Points::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Points $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Points $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * [
     *      count Points,
     *      data | false
     *      view as cluster
     * ]
     */
    public function getInBounds(Bounds $bounds)
    {
        $_data = [
            'count' => 0,
            'data' => false,
            'cluster' => false,
        ];

        $count = $this->getCountByBounds($bounds);
        $_data['count'] = $count;

        // только подсчёт количества точек
        if (Bounds::MAX_POINTS_CLUSTER < $count) {
            return $_data;
        }

        // данные о точках для кластеризованного (на клиенте) показа
        if (Bounds::MIN_POINTS_CLUSTER < $count && $count < Bounds::MAX_POINTS_CLUSTER) {
            $_data['cluster'] = true;
        }

        $_data['data'] = $this->getPointsByBounds($bounds);

        return $_data;
    }

    /**
     * Count points by Bounds
     */
    public function getCountByBounds(Bounds $bounds)
    {
        $sql = "select count(*) as count_id from %s where location <@ box(point(:x1,:y1),point(:x2,:y2))";

        $rsm = new ResultSetMapping;

        $rsm->addEntityResult('App\Entity\Points', 'c');
        $rsm->addFieldResult('c', 'count_id', 'id');
        $rsm->addFieldResult('c', 'location', 'location');

        $tableName = $this->_em->getClassMetadata(Points::class)->getTableName();
        $sql = sprintf($sql, $tableName);

        $res = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameters([
                'x1' => $bounds->getSouthWestLat(),
                'y1' => $bounds->getSouthWestLng(),
                'x2' => $bounds->getNorthEastLat(),
                'y2' => $bounds->getNorthEastLng(),
            ]);
        
        return $res->getSingleScalarResult();
    }

    /**
     * Points list by Bounds
     */
    public function getPointsByBounds(Bounds $bounds)
    {
        $tableName = $this->_em->getClassMetadata(Points::class)->getTableName();
        $sql = "select id, location from ". $tableName ." where location <@ box(point(:x1,:y1),point(:x2,:y2))";

        $rsm = new ResultSetMapping;

        $rsm->addEntityResult('App\Entity\Points', 'c');
        $rsm->addFieldResult('c', 'id', 'id');
        $rsm->addFieldResult('c', 'location', 'location');

        $res = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameters([
                'x1' => $bounds->getSouthWestLat(),
                'y1' => $bounds->getSouthWestLng(),
                'x2' => $bounds->getNorthEastLat(),
                'y2' => $bounds->getNorthEastLng(),
            ]);

        return $res->getArrayResult();
    }

    // /**
    //  * @return Points[] Returns an array of Points objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Points
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
