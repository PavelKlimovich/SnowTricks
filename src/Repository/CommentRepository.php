<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Trick[] Returns an array of Trick objects
    */
    public function getComments($trick, $limit): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')   
            ->where('c.trick = '.$trick)
                ->orderBy('c.id', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult(0+$limit) 
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Trick[] Returns an array of Trick objects
    */
    public function getFirstComments($trick): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')  
            ->where('c.trick = '.$trick)
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(10)
                ->setFirstResult(0) 
            ->getQuery()
            ->getResult();
    }
}
