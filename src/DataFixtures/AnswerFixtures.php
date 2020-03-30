<?php


namespace App\DataFixtures;


use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AnswerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
        for ($i = 0; $i < 30; $i++) {
            $question = $this->getReference(QuestionFixtures::getReferenceKey($i % 10));
            $answer = new Answer();
            $answer->setAnswerId(mt_rand(1,5));
            $tags = ['a', 'b', 'c', 'd'];
            $answer->setTags($tags[array_rand($tags)]);
            $answer->setAnswer("This is answer ". $i);
            $answer->setQuestion($question);
            $manager->persist($answer);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            QuestionFixtures::class
        ];
    }
}