<?php

namespace App\Repository;

use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 *
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    public function save(RendezVous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RendezVous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return RendezVous[] Returns an array of RendezVous objects
    //     */
    public function findByDocteur($value): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.docteur = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    public function findByPatient($value): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.patient = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByPatientAndDoctor(int $patient, int $doctor): ?RendezVous
    {
        return $this->createQueryBuilder('rv')
            ->where('rv.patient = :patient')
            ->andWhere('rv.docteur= :doctor')
            ->setParameter('patient', $patient)
            ->setParameter('doctor', $doctor)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function countByDate()
    {

        $query = $this->getEntityManager()->createQuery("
            SELECT SUBSTRING(a.date, 1, 10) as date, COUNT(a) as count FROM App\Entity\RendezVous a GROUP BY date
        ");
        return $query->getResult();
    }
}
