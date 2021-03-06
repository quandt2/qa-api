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
            "question" => "are you ok?",
            "rank" => 3
        );
        $response = $this->client->post('/api/question/create', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Server'));
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('success', $finishedData);
        $this->assertArrayHasKey('detail', $finishedData);
        $this->assertEquals($data["question"], $finishedData["detail"]["question"]);
        $this->assertEquals($data["rank"], $finishedData["detail"]["rank"]);
    }

    public function testGetQuestionSuccess()
    {
        $data = array(
            "rank" => "ASC",
            "itemPerPage" => 5,
            "pageNumber" => 1
        );
        $response = $this->client->get('/api/question/get', [
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
