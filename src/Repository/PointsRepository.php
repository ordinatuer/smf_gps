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

    public function getCountByBounds(Bounds $bounds)
    {
        $rsm = new ResultSetMapping;

        $rsm->addEntityResult('App\Entity\Points', 'c');
        $rsm->addFieldResult('c', 'count_id', 'id');
        $rsm->addFieldResult('c', 'location', 'location');

        $tableName = $this->_em->getClassMetadata(Points::class)->getTableName();

        $sql = "select count(id) as count_id from " .$tableName. " where location <@ box(point(:x1,:y1),point(:x2,:y2))";

        $res = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('x1', $bounds->getSouthWestLat())
            ->setParameter('y1', $bounds->getSouthWestLng())
            ->setParameter('x2', $bounds->getNorthEastLat())
            ->setParameter('y2', $bounds->getNorthEastLng())
            ->getSingleScalarResult();
        
        return $res;
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
