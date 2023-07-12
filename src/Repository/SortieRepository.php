<?php

namespace App\Repository;

use App\Entity\Sortie;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function search(
        $user,
        $nom,
        $dateDebut,
        $dateFin,
        $checkboxs
    ): array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.dateHeureDebut', 'DESC');

        if ($user->getCampus()) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $user->getCampus());
        }
        if ($nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }
        if ($dateDebut) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        } else {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', new DateTimeImmutable("-1 month"));
        }
        if ($dateFin) {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        if ($checkboxs["isOrganisateur"]) {
            $qb->andWhere('s.organisateur = :f1')
                ->setParameter('f1', $user);
        }
        if ($checkboxs['isInscrit'] && $checkboxs['isNotInscrit']) {
            $qb->leftJoin('s.participants', 'i')
                ->andWhere('i = :g1 OR i != :g1')
                ->setParameter('g1', $user);
        } else {
            if ($checkboxs['isInscrit']) {
                $qb->leftJoin('s.participants', 'i1')
                    ->andWhere('i1 = :h1')
                    ->setParameter('h1', $user);
            }
            if ($checkboxs['isNotInscrit']) {
                $qb->leftJoin('s.participants', 'i2')
                    ->andWhere('i2 != :j1 OR i2 IS NULL')
                    ->setParameter('j1', $user);
            }
        }
        if ($checkboxs['isPast']) {
            $qb->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTimeImmutable('now'));
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}