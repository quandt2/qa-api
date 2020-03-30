<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Question;

class QuestionFixtures extends Fixture
{
    public static function getReferenceKey($i)
    {
        return sprintf('question_id_%s', $i);
    }

    public function load(ObjectManager $manager)
    {
        // create 20 questions
        for ($i = 0; $i < 10; $i++) {
            $question = new Question();
            $question->setQuestion("This is question " . $i);
            $question->setRank(mt_rand(1, 20));
            $manager->persist($question);
            $this->addReference(self::getReferenceKey($i), $question);
        }

        $manager->flush();
    }

}
