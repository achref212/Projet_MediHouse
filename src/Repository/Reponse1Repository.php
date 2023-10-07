<?php

namespace App\Repository;

use App\Entity\Reponse1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reponse1>
 *
 * @method Reponse1|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reponse1|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reponse1[]    findAll()
 * @method Reponse1[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Reponse1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse1::class);
    }

    public function save(Reponse1 $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reponse1 $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function get_Reponse1($id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery("SELECT r FROM App\Entity\Reponse1 r JOIN r.reclamation f  WHERE f.id=:id")
            ->setParameter('id', $id);
        return $query->getOneOrNullResult();
    }


    //    /**
    //     * @return Reponse1[] Returns an array of Reponse1 objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reponse1
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
