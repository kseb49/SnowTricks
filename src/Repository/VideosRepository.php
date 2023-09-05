<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Videos>
 *
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videos::class);
    }


     /**
     * Get all the videos of a trick
     *
     * @param integer $id
     * @return array
     */
    public function findAllVideos(int $id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'SELECT v.src
        FROM App\Entity\Figures f
        INNER JOIN f.videos v
        WHERE f.id = :id'
        )->setParameter('id', $id);
        return $query->getResult();

    }


    public function removeVideos(string $video_src): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'DELETE FROM App\Entity\Videos v
        WHERE v.src = :video_src'
        )->setParameter('video_src', $video_src);
        return $query->getResult();

    }
//    /**
//     * @return Videos[] Returns an array of Videos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Videos
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
