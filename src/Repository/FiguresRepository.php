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

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
        SELECT f,SUBSTRING(f.description,1,50) descr, u, g from App\Entity\Figures f
         join f.users_id u
         join f.groups_id g
         ORDER BY f.creation_date DESC');
        $query->getResult();
        return $query->getResult();

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


}
