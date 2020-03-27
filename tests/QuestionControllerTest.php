<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
class QuestionControllerTest extends TestCase
{
    public function testCreateQuestion()
    {
        $url = $_ENV['APP_URL'];
        $client = new Client([
            'base_uri' => 'http://localhost:8000',
            'headers' => [
                'Accept' => 'application/json; charset=utf-8'
            ]
        ]);

        $data = array(
            'question' => 'are you ok?',
            'rank' => 3
        );
        $response = $client->post('/api/createQuestion', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Server'));
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('success', $finishedData);
        $this->assertArrayHasKey('detail', $finishedData);
        $this->assertEquals($data["question"], $finishedData["success"]["question"]);
        $this->assertEquals($data["rank"], $finishedData["success"]["rank"]);

    }
}
