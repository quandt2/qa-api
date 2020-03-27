<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $answer_id;

    /**
     * @ORM\Column(type="text")
     */
    private $answer;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id",referencedColumnName="id", nullable=false)
     */
    private $question;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswerId(): ?int
    {
        return $this->answer_id;
    }

    public function setAnswerId(int $answer_id): self
    {
        $this->answer_id = $answer_id;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getQuestionId(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function toArray() {
        return [
            "id" => $this->id,
            "answer_id" => $this->answer_id,
            "answer" => $this->answer
        ];
    }
}
