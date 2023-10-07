<?php

namespace App\Repository;

use App\Entity\Figures;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Figures>
 *
 * @method Figures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figures[]    findAll()
 * @method Figures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiguresRepository extends ServiceEntityRepository
{
    /**
     * @var int Number of tricks requested
     */
    public const PAGINATOR_PER_PAGE = 6;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figures::class);
    }


    public function findForHome()
    {
        
        /******* */
        // $conn = $this->getEntityManager()->getConnection();
        // $sql = '
        // SELECT *,
        // f.name as figure,
        // u.name AS username,
        // SUBSTRING(f.description,100)
        // FROM figures f
        // left join users u on u.id = f.users_id
        // JOIN `groups` g on g.id = f.groups_id
        // ORDER BY creation_date DESC
        // ';
        // $result = $conn->executeQuery($sql);
        // return $result->fetchAllAssociative();

        /******* */
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
        SELECT f,SUBSTRING(f.description,1,50) descr, u, g from App\Entity\Figures f
         join f.users_id u
         join f.groups_id g
         ORDER BY f.creation_date DESC');
        $query->getResult();
        // dd($query->getResult());
        return $query->getResult();

        // $qb = $this->createQueryBuilder('f')
        // ->addSelect('u')
        // ->addSelect('g')
        // ->join('f.users_id')


    }


    /**
    * Get messages according to the offset
    *
    * @param integer $offset
    * @return Paginator
    */
   public function findPaginated(int $offset): Paginator
   {
       $query =  $this->createQueryBuilder('f')
           ->orderBy('f.creation_date','DESC')
           ->setMaxResults(self::PAGINATOR_PER_PAGE)
           ->setFirstResult($offset)
           ->getQuery()
       ;
       return new Paginator($query);

   }
//    /**
//     * @return Figures[] Returns an array of Figures objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Figures
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
