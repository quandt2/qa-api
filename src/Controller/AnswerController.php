<?php

namespace App\Controller;

use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class AnswerController extends AbstractController
{
    protected $answerRepository;
    protected $questionRepository;
    protected $logger;

    public function __construct(AnswerRepository $answerRepository, QuestionRepository $questionRepository, LoggerInterface $logger)
    {
        $this->answerRepository = $answerRepository;
        $this->questionRepository = $questionRepository;
        $this->logger = $logger;
    }


    public function index()
    {
        return $this->render('answer/index.html.twig', [
            'controller_name' => 'AnswerController',
        ]);
    }

    /**
     * Create the answer
     *
     * @Route("/createAnswer", name="createAnswer", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of created ",
     *     @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="UUID"),
     *         @SWG\Property(property="answer_id", type="integer"),
     *         @SWG\Property(property="answer", type="string"),
     *         @SWG\Property(property="tags", type="string")
     *     )
     * )
     * @SWG\Parameter(
     *         name="question",
     *         in="body",
     *         type="string",
     *         description="Enter the question info",
     *         required=true,
     *          @SWG\Schema(
     *             @SWG\Property(property="id", type="integer", description="UUID"),
     *             @SWG\Property(property="answer_id", type="integer"),
     *             @SWG\Property(property="answer", type="string"),
     *             @SWG\Property(property="tags", type="string"),
     *             @SWG\Property(property="question_id", type="integer")
     *     )
     * )
     *
     */
    public function createAnswer(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $question_id = $data["question_id"];
            $question = $this->questionRepository->find($question_id);
            if ($question == null) {
                throw New NotFoundHttpException("Not found question");
            }

            $answerObj = $this->answerRepository->createAnswer($data, $question);

            return new JsonResponse([
                "success" => "question is created",
                "detail" => [
                    "id" => $answerObj->getId(),
                    "answer_id" => $answerObj->getAnswerId(),
                    "answer" => $answerObj->getAnswer(),
                    "tags" => $answerObj->getTags()
                ]], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
    }
}
