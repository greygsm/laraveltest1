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
    }
}
