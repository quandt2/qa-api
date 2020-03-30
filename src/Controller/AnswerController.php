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

    /**
     * get the list of question and its answer
     * @Route("/answer/get", name="answer", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of created ",
     *     @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="id"),
     *             @SWG\Property(property="answer_id", type="integer"),
     *              @SWG\Property(property="answer", type="string"),
     *              @SWG\Property(property="question_id", type="string"),
     *             @SWG\Property(property="tags", type="string")
     *     )
     * )
     * @SWG\Parameter(
     *          name="sortBy",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="sort by id or rank",
     *          enum={"id", "anwer_id", "tags", "question_id"}
     *
     *      ),
     *
     * @SWG\Parameter(
     *          name="orderBy",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="orderby rank",
     *          enum={"ASC", "DESC"}
     *
     *      ),
     *
     * @SWG\Parameter(
     *          name="itemPerPage",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="number of item inn page",
     *          default="10"
     *      ),
     *
     * @SWG\Parameter(
     *          name="pageNumber",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="Page number",
     *          default="1"
     *      ),
     */
    public function index(Request $request)
    {
        try {
            $itemPerPage = !empty($request->query->get("itemPerPage")) ? $request->query->get("itemPerPage") : 10;
            $pageNumber = !empty($request->query->get("pageNumber")) ? $request->query->get("pageNumber") : 1;
            $sortBy = !empty($request->query->get("sortBy")) ? $request->query->get("sortBy") : "id" ;
            $orderBy = !empty($request->query->get("orderBy")) ? $request->query->get("orderBy") : "ASC";

            $offset = ($pageNumber - 1) * $itemPerPage;

            $allAnswer = $this->answerRepository->findBy([], [$sortBy => $orderBy], $itemPerPage, $offset);
            $result = [];

            foreach ($allAnswer as $answer) {
                $result[] = $answer->toArray();
            }
            return new JsonResponse([
                "success" => true,
                "data" => $result
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 400);
        }
    }

    /**
     * Create the answer
     *
     * @Route("/answer/create", name="createAnswer", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of created ",
     *     @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="UUID"),
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
            $question_id = isset($data["question_id"]);
            $question = $this->questionRepository->find($question_id);
            if ($question == null) {
                throw New NotFoundHttpException("Not found question");
            }

            $answerObj = $this->answerRepository->createAnswer($data, $question);

            return new JsonResponse([
                "success" => "answer is created",
                "detail" => [
                    "id" => $answerObj->getId(),
                    "answer_id" => $answerObj->getAnswerId(),
                    "answer" => $answerObj->getAnswer(),
                    "tags" => $answerObj->getTags()
                ]], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 400);
        }
    }
}
