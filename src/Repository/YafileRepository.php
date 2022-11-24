<?php

namespace App\Repository;

use App\Entity\Yafile;
use App\Service\YafileUploader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method Yafile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Yafile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Yafile[]    findAll()
 * @method Yafile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YafileRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private YafileUploader $yafileUploader,
    )
    {
        
        parent::__construct($registry, Yafile::class);
    }

    public function uploadAddress(UploadedFile $file, Yafile $address)
    {
        $filename = $this->yafileUploader->upload($file, YafileUploader::ADDRESS_DIR);
        $address->setName($filename);

        $this->add($address);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Yafile $entity, bool $flush = true): void
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
    public function remove(Yafile $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Yafile[] Returns an array of Yafile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Yafile
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
