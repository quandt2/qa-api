<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Answer::class);
        $this->manager = $manager;
    }

    public function createAnswer($data, Question $question) {
        $lastAnswer = $this->getLastAnswerId($data["question_id"]);
        $lastId = 0;
        if ($lastAnswer != null) {
            $lastId = $lastAnswer['answer_id'] + 1;
        }

        $answerObj = new Answer();
        $answerObj->setAnswerId($lastId)->setAnswer($data["answer"])->setQuestion($question)->setTags($data["tags"]);

        $this->manager->persist($answerObj);
        $this->manager->flush();
        return $answerObj;
    }

    public function getLastAnswerId($question_id) {
        return $this->createQueryBuilder('a')
            ->select('a.answer_id' )
            ->andWhere('a.question = :val')
            ->setParameter('val', $question_id)
            ->orderBy('a.answer_id', 'DESC')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
