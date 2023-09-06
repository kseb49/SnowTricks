<?php

namespace App\Repository;

use App\Entity\Images;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Images>
 *
 * @method Images|null find($id, $lockMode = null, $lockVersion = null)
 * @method Images|null findOneBy(array $criteria, array $orderBy = null)
 * @method Images[]    findAll()
 * @method Images[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagesRepository extends ServiceEntityRepository
{

    //  /**
    //  * The default image used when the request hasn't any
    //  */
    // const DEFAULT_IMG = "snow_board.jpeg";


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Images::class);
    }

    /**
     * Get the numbers of images for a trick
     *
     * @param integer $id Identifier of the trick
     * @return array
     */
    public function countImages(int $id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'SELECT COUNT(i.id)
        FROM App\Entity\Figures f
        INNER JOIN f.images i
        WHERE f.id = :id'
        )->setParameter('id', $id);
        return $query->getOneOrNullResult();

    }

    /**
     * Get all the images of a trick
     *
     * @param integer $id
     * @return array
     */
    public function findAllImages(int $id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'SELECT i.image_name
        FROM App\Entity\Figures f
        INNER JOIN f.images i
        WHERE f.id = :id'
        )->setParameter('id', $id);
        return $query->getResult();

    }


    public function removeImages(string $image_name): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'DELETE FROM App\Entity\Images i
        WHERE i.image_name = :image_name'
        )->setParameter('image_name', $image_name);
        return $query->getResult();

    }
//    /**
//     * @return Images[] Returns an array of Images objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Images
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
