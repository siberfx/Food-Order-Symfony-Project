<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
//** left join with sql**/ */

    public function getUserReservation($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT o.*,r.title as rname,u.title as fname  FROM reservation o
        JOIN restaurant r ON r.id = o.restaurantid
        JOIN food u ON u.id = o.foodid
        WHERE o.userid =:userid
        ORDER BY o.id DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userid'=>$id]);

        return $stmt->fetchAll();
    }

    public function getReservation($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT o.*,r.title as rname,u.title as fname, usr.name as uname  FROM reservation o
        JOIN restaurant r ON r.id = o.restaurantid
        JOIN food u ON u.id = o.foodid
         JOIN user usr ON usr.id = o.userid
        WHERE o.id = :id 
        
         
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=> $id]);

        return $stmt->fetchAll();
    }

   public function getReservations($status): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT o.*,r.title as rname,u.title as fname,usr.name as uname FROM reservation o
        JOIN restaurant r ON r.id = o.restaurantid
        JOIN food u ON u.id = o.foodid
        JOIN user usr ON usr.id = o.userid
        WHERE o.status =:status
     
         
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['status' => $status]);

        return $stmt->fetchAll();
    }


  /*  public function getReservations($status): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o.id,o.userid,o.restaurantid,o.foodid,o.name,o.surname,o.email,o.phone,o.status')
            ->leftJoin('App\Entity\Restaurant', 'r', 'WITH', 'r.id = o.id')
            ->leftJoin('App\Entity\Admin\Food', 'f', 'WITH', 'f.id = o.id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.id = o.id')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.id', 'DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }*/


}
