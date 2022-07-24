<?php

namespace App\Repository;

use App\Entity\Corruption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

use App\geo\Objects\Bounds;

/**
 * @method Corruption|null find($id, $lockMode = null, $lockVersion = null)
 * @method Corruption|null findOneBy(array $criteria, array $orderBy = null)
 * @method Corruption[]    findAll()
 * @method Corruption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorruptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Corruption::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Corruption $entity, bool $flush = true): void
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
    public function remove(Corruption $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Bounds $bounds
     */
    public function findByBounds(Bounds $bounds) {
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('App\Entity\Corruption', 'c');
        $rsm->addFieldResult('c', 'id', 'id');
        $rsm->addFieldResult('c', 'location', 'location');
        $rsm->addFieldResult('c', 'address_street', 'address_street');
        $rsm->addFieldResult('c', 'address_city', 'address_city');
        $rsm->addFieldResult('c', 'location_latitude', 'location_latitude');
        $rsm->addFieldResult('c', 'location_longitude', 'location_longitude');

        $sql = "select id, address_city, address_street, location_latitude, location_longitude from corruption where location <@ box(point(:x1,:y1),point(:x2,:y2)) limit 1000";

        $res = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('x1', $bounds->getSouthWestLat())
            ->setParameter('y1', $bounds->getSouthWestLng())
            ->setParameter('x2', $bounds->getNorthEastLat())
            ->setParameter('y2', $bounds->getNorthEastLng())
            ->getResult();
        
        return $res;
    }

    public function getCountByBounds(Bounds $bounds)
    {
        $rsm = new ResultSetMapping;

        $rsm->addEntityResult('App\Entity\Corruption', 'c');
        $rsm->addFieldResult('c', 'count_id', 'id');
        $rsm->addFieldResult('c', 'location', 'location');

        $sql = "select count(id) as count_id from corruption where location <@ box(point(:x1,:y1),point(:x2,:y2))";

        $res = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('x1', $bounds->getSouthWestLat())
            ->setParameter('y1', $bounds->getSouthWestLng())
            ->setParameter('x2', $bounds->getNorthEastLat())
            ->setParameter('y2', $bounds->getNorthEastLng())
            ->getSingleScalarResult();
        
        return $res;
    }

    // /**
    //  * @return Corruption[] Returns an array of Corruption objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Corruption
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
