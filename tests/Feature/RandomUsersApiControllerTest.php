<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RandomUsersApiControllerTest extends TestCase
{

    public function testRandomUsersApiEndpoint()
    {
        // Mock response from third-party server
        Http::fake([
            'https://randomuser.me/api/*' => Http::response([
                'results' => [
                    [
                        'name' => [
                            'title' => 'Mr',
                            'first' => 'John',
                            'last' => 'Doe',
                        ],
                        'location' => [
                            'street' => [
                                'number' => 123,
                                'name' => 'Main St',
                            ],
                            'city' => 'City',
                            'state' => 'State',
                            'country' => 'USA',
                        ],
                        'email' => 'john.doe@example.com',
                        'phone' => '(123) 456-7890',
                    ],
                ],
            ]),
        ]);

        // Sending request to API
        $response = $this->getJson(route('api.randomusers', [
            'fields' => ['name', 'phone', 'email', 'location'],
            'user_qty' => 10,
            'format' => 'json',
            'sort_by' => 'last',
            'sort_order' => 'asc',
        ]));

        // Check if response OK
        $response->assertStatus(200);

        // Checking structure of response
        $response->assertJsonStructure([
                '*' => [
                    'full_name',
                    'phone',
                    'email',
                    'country',
                ]
        ]);

        // Checking data after processing
        $this->assertEquals([
            [
                'full_name' => 'John Doe',
                'phone' => '(123) 456-7890',
                'email' => 'john.doe@example.com',
                'country' => 'USA',
            ]
        ], $response->json());

        // Sending request to API with XML format
        $responseXml = $this->get(route('api.randomusers', [
            'fields' => ['name', 'phone', 'email', 'location'],
            'user_qty' => 10,
            'format' => 'xml', // Set format to XML
            'sort_by' => 'last',
            'sort_order' => 'asc',
        ]));

        // Check if response OK
        $responseXml->assertStatus(200);

        // Convert XML to array for easier comparison
        $xmlArray = simplexml_load_string($responseXml->getContent(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonArray = json_decode(json_encode($xmlArray), true);

        // Checking structure of response for XML
        $this->assertArrayHasKey('numeric_0', $jsonArray);
        $this->assertArrayHasKey('full_name', $jsonArray['numeric_0']);
        $this->assertArrayHasKey('phone', $jsonArray['numeric_0']);
        $this->assertArrayHasKey('email', $jsonArray['numeric_0']);
        $this->assertArrayHasKey('country', $jsonArray['numeric_0']);

        // Checking data after processing for XML
        $this->assertEquals([
            'full_name' => 'John Doe',
            'phone' => '(123) 456-7890',
            'email' => 'john.doe@example.com',
            'country' => 'USA',
        ], $jsonArray['numeric_0']);
    }
}
