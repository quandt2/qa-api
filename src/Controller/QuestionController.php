<?php

namespace App\Controller;

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
class QuestionController extends AbstractController
{
    private $questionRepository;

    private $logger;

    public function __construct(QuestionRepository $questionRepository, LoggerInterface $logger)
    {
        $this->questionRepository = $questionRepository;
        $this->logger = $logger;
    }

    /**
     * get the list of question and its answer
     * @Route("/question/get", name="question", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of created ",
     *     @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="UUID"),
     *             @SWG\Property(property="question", type="string"),
     *             @SWG\Property(property="rank", type="integer")
     *
     *     )
     * )
     * @SWG\Parameter(
     *          name="sortBy",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="sort by id or rank",
     *          enum={"id", "rank"}
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
    public function getQuestion(Request $request)
    {
        try {
            $itemPerPage = !empty($request->query->get("itemPerPage")) ? $request->query->get("itemPerPage") : 10;
            $pageNumber = !empty($request->query->get("pageNumber")) ? $request->query->get("pageNumber") : 1;
            $sortBy = !empty($request->query->get("sortBy")) ? $request->query->get("sortBy") : "id" ;
            $orderBy = !empty($request->query->get("orderBy")) ? $request->query->get("orderBy") : "ASC";


            //$total = $this->questionRepository->count($search);
            $offset = ($pageNumber - 1) * $itemPerPage;

            $allQuestion = $this->questionRepository->findBy([], [$sortBy => $orderBy], $itemPerPage, $offset);

            $result = [];
            foreach ($allQuestion as $question) {
                $answerList = $question->getAnswers();
                $answer = [];
                foreach ($answerList as $ans) {
                    $answer[] = $ans->toArray();
                }
                $result[] = [
                    "id" => $question->getId(),
                    "question" => $question->getQuestion(),
                    "answer" => $answer,
                    "rank" => $question->getRank()
                ];
            }
            return new JsonResponse([
                "success" => true,
                "data" => $result

                ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create the question
     *
     * @Route("/question/create", name="createQuestion", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of created ",
     *     @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="UUID"),
     *             @SWG\Property(property="question", type="string"),
     *             @SWG\Property(property="rank", type="integer")
     *     )
     * )
     * @SWG\Parameter(
     *         name="question",
     *         in="body",
     *         type="string",
     *         description="Enter the question info",
     *         required=true,
     *          @SWG\Schema(
     *             @SWG\Property(property="question", type="string"),
     *             @SWG\Property(property="rank", type="integer")
     *     )
     * )
     *
     */
    public function create(Request $request) : JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $question = isset($data["question"]) ? $data["question"] : "";
            $rank = isset($data["rank"]) ? $data["rank"] : "";

            if (empty($question) || empty($rank)) {
                throw new NotFoundHttpException("Expecting not empty parameters");
            }
            $questionObj = $this->questionRepository->createQuestion($question, $rank);

            return new JsonResponse([
                "success" => "question is created",
                "detail" => [
                    "id" => $questionObj->getId(),
                    "question" => $questionObj->getQuestion(),
                    "rank" => $questionObj->getRank()
                ]], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
