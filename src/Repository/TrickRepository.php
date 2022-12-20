<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function save(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Trick[] Returns an array of Trick objects
    */
    public function findByExampleField($value): array
    {
        
        return $this->createQueryBuilder('t') // select * from tricks as t
        ->select('t.id ')
                ->andWhere('t.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('t.id', 'ASC')
                ->setMaxResults(10)
                ->setFirstResult(10)
                ->getQuery()
                ->getResult();
    }

    /**
     * @return Trick[] Returns an array of Trick objects
    */
    public function getTricks($limit): array
    {
        return $this->createQueryBuilder('t') // select * from tricks as t
                ->select('t')
                    ->orderBy('t.id', 'ASC')
                    ->setMaxResults($limit)
                    ->setFirstResult(0 + $limit)
                ->getQuery()
                ->getResult();
    }

    /**
     * @return Trick[] Returns an array of Trick objects
    */
    public function getFirstTricks(): array
    {
        return $this->createQueryBuilder('t') // select * from tricks as t
                ->select('t')
                    ->orderBy('t.id', 'ASC')
                    ->setMaxResults(5)
                    ->setFirstResult(0)
                ->getQuery()
                ->getResult();
    }

}
