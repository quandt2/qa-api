<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class QuestionControllerTest extends TestCase
{

    protected $client;

    protected function setUp()
    {
        $url = $_ENV['APP_URL'];
        $this->client = new Client([
            'base_uri' => "http://192.168.0.3",
            'headers' => [
                'Accept' => 'application/json; charset=utf-8'
            ]
        ]);
    }

    public function testCreateQuestionSuccess()
    {
        $data = array(
            "answer" => "Answer for test",
            "tags" => "a",
            "question_id" => 1
        );
        $response = $this->client->post('/api/answer/create', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('success', $finishedData);
        $this->assertArrayHasKey('detail', $finishedData);
        $this->assertEquals($data["answer"], $finishedData["detail"]["answer"]);
        $this->assertEquals($data["tags"], $finishedData["detail"]["tags"]);
    }

    public function testGetAnswerSuccess()
    {
        $data = array(
            "tags" => "ASC",
            "itemPerPage" => 5,
            "pageNumber" => 1
        );
        $response = $this->client->get('/api/answer/get', [
            'query' => $data
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('success', $finishedData);
        $this->assertArrayHasKey('data', $finishedData);
        $this->assertEquals(true, $finishedData["success"]);
        $this->assertEquals(5, count($finishedData["data"]));
    }

}
