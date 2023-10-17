<?php

namespace App\Repository;

use App\Entity\Figures;
use App\Entity\Messages;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{

    /**
     * @var int Number of messages requested
     */
    public const PAGINATOR_PER_PAGE = 10;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);

    }


   /**
    * Get messages according to the offset
    *
    * @param Figures $figures
    * @param integer $offset
    * @return Paginator
    */
    public function findPaginated(Figures $figures, int $offset): Paginator
    {
        $query =  $this->createQueryBuilder('m')
            ->andWhere('m.figures = :val')
            ->setParameter('val', $figures)
            ->orderBy('m.message_date','DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;
        return new Paginator($query);

    }


}
