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
     * @Route("/question", name="question", methods={"POST"})
     */
    public function index(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $search = $data["search"];
            $orderBy = !empty($data["orderBy"]) ? $data["orderBy"] : ["id" => "ASC"];
            //$pagination = $data["pagination"];
            $itemPerPage = isset($data["itemPerPage"]) ? $data["itemPerPage"] : 10;
            $pageNumber = isset($data["pageNumber"]) ? $data["pageNumber"] : 1;
            //$total = $this->questionRepository->count($search);
            $offset = ($pageNumber - 1) * $itemPerPage;

            $allQuestion = $this->questionRepository->findBy($search, $orderBy, $itemPerPage, $offset);

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
                    "answer" => $answer
                ];
            }
            return new JsonResponse([
                "success" => true,
                "detail" => [
                    "question" => json_encode($result, true)
                ]], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
    }

    /**
     * Create the question
     *
     * @Route("/createQuestion", name="createQuestion", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Question::class))
     *     )
     * )
     * @SWG\Parameter(
     *         name="question",
     *         in="body",
     *         description="Enter the question",
     *         @Model(type=Question::class)
     *
     * )
     */
    public function create(Request $request) : JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->logger->info(json_encode($data));
            $question = $data["question"];
            $this->logger->info($question);
            $rank = $data["rank"];

            if (empty($question) || empty($rank)) {
                throw new NotFoundHttpException("Expecting not empty parameters");
            }
            $questionObj = $this->questionRepository->createQuestion($question, $rank);

            return new JsonResponse([
                "success" => "question is created",
                "detail" => [
                    "question" => $questionObj->getQuestion(),
                    "rank" => $questionObj->getRank()
                ]], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
    }
}
