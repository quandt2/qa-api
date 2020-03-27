<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Question::class);
        $this->manager = $manager;
    }

    public function createQuestion($question, $rank) {
        $questionObj = new Question();
        $questionObj->setQuestion($question)
            ->setRank($rank);
        $this->manager->persist($questionObj);
        $this->manager->flush();
        return $questionObj;
    }

    public function pagination(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        $qb = $this->createQueryBuilder('a');
        $query=$qb->getQuery();
// SHOW SQL:
        echo $query->getSQL();
// Show Parameters:
        echo $query->getParameters();
    }

    public function findByTest(array $criteria, array $orderBy = null, $limit = null, $offset = null) {

    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
