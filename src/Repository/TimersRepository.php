<?php

namespace App\Repository;

use App\Entity\Timers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Timers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timers[]    findAll()
 * @method Timers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timers::class);
    }

    // /**
    //  * @return Timers[] Returns an array of Timers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Timers
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param int $id
     * @return mixed
     */
    public function getTimersFromUserId($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT t.id,t.user_id,t.start_date,t.end_date,t.status,t.timer_type,type.type,t.title,t.description,type.duration,t.first_cycle
        FROM timers as t,timer_type as type
        WHERE t.timer_type=type.id AND user_id=:id 
        ORDER BY t.id 
        DESC LIMIT 1;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getTomatosFromUserId($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT t.id,t.user_id,t.start_date,t.end_date,t.status,t.timer_type,type.type,t.title,t.description,type.duration,t.first_cycle
        FROM timers as t,timer_type as type
        WHERE t.timer_type=type.id AND user_id=:id AND type.type="tomato"
        ORDER BY t.id 
        DESC LIMIT 1;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getTomatosCycle($idUser): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT type.type,type.duration,t.status
                FROM timers as t,timer_type as type
                WHERE t.timer_type=type.id AND user_id=:idUser AND t.status!='broken' AND t.id >= (SELECT id FROM timers WHERE first_cycle='yes' ORDER BY id DESC LIMIT 1)
                ORDER BY t.id
                ASC LIMIT 6";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idUser' => $idUser]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getCycle($idUser): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT type.type,type.duration,t.status
                FROM timers as t,timer_type as type
                WHERE t.timer_type=type.id AND user_id=:idUser AND t.status='done' AND t.id >= (SELECT id FROM timers WHERE first_cycle='yes' ORDER BY id DESC LIMIT 1)
                ORDER BY t.id
                ASC LIMIT 6";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idUser' => $idUser]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @param $date
     * @return array
     */
    public function getLastEvent($idUser,$date): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT t.start_date,TIMEDIFF(t.end_date,t.start_date) AS duration,t.status,t.title,t.description,type.type
                FROM timers as t,timer_type as type
                WHERE t.timer_type=type.id AND user_id=:idUser AND t.status!='doing' AND date(t.start_date)=date(:date)
                ORDER BY t.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idUser' => $idUser, 'date' => $date]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @param int $idUser
     * @return true is the first of the day, else false
     */
    public function getTimerFirstDay($idUser) {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT t.start_date
        FROM timers as t
        WHERE user_id=:id AND DATE(start_date)=CURRENT_DATE()
        GROUP BY t.start_date
        HAVING COUNT(t.start_date)>=1';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $idUser]);

        if(!$stmt->fetchAll()) {
            return true;
        }

        return false;
    }

    public function getTimerByDateAndStatus($idUser,$startDate,$status) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT end_date
                FROM timers
                WHERE  user_id=:idUser AND DATE(start_date)=:startDate AND status=:status";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idUser' => $idUser, 'startDate' => $startDate, 'status' => $status]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}
