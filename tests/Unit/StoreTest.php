<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Store;

class StoreTest extends TestCase
{
    protected $user;
    protected $token;
    protected $headers;

    protected static $initialTreeStructure;

    public static function setUpBeforeClass(){
        self::$initialTreeStructure = [
            'tree' => [
                //node A
                [
                    'id' => 1, 'parent_id' => 0, 'store_name' => 'A',
                    'children' => [
                        [
                            'id' => 4, 'parent_id' => 1, 'store_name' => 'D'
                        ],
                        [
                            'id' => 5, 'parent_id' => 1, 'store_name' => 'E'
                        ]
                    ]
                ],
                //node B
                [
                    'id' => 2, 'parent_id' => 0, 'store_name' => 'B',
                    'children' => [
                        [
                            'id' => 6, 'parent_id' => 2, 'store_name' => 'F'
                        ],
                        [
                            'id' => 7, 'parent_id' => 2, 'store_name' => 'G',
                            'children' => [
                                ['id' => 12, 'parent_id' => 7, 'store_name' => 'L']
                            ]
                        ],
                        [
                            'id' => 8, 'parent_id' => 2, 'store_name' => 'H'
                        ]
                    ]
                ],
                //node C
                [
                    'id' => 3, 'parent_id' => 0, 'store_name' => 'C',
                    'children' => [
                        [
                            'id' => 9, 'parent_id' => 3, 'store_name' => 'I',
                            'children' => [
                                [
                                    'id' => 10, 'parent_id' => 9, 'store_name' => 'J'
                                ],
                                [
                                    'id' => 11, 'parent_id' => 9, 'store_name' => 'K'
                                ]
                            ]
                        ]
                    ]
                ]//END - node c
            ]
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->token = $this->user->generateToken();
        $this->headers = ['Authorization' => "Bearer ".$this->token];
    }

    /** @test */
    public function all_stores_are_listed_correctly()
    {
        //TEST - list all stores in tree structure

        $response = $this->json('GET', '/api/stores', [], $this->headers)
            ->assertStatus(200)
            ->assertJson(self::$initialTreeStructure);
    }

    /** @test */
    public function specific_store_and_children_are_listed_correctly()
    {
        //TEST - view a specific store and its children

        $expectedTree = [
            'store_and_children' => [
                'id' => 1, 'parent_id' => 0, 'store_name' => 'A',
                'children' => [
                    [
                        'id' => 4, 'parent_id' => 1, 'store_name' => 'D'
                    ],
                    [
                        'id' => 5, 'parent_id' => 1, 'store_name' => 'E'
                    ]
                ]
            ]
        ];

        $store = Store::where('store_name', 'A')->first(); //id => 1, get store A and its children

        $this->json('GET', '/api/stores/'.$store->id, [], $this->headers)
            ->assertStatus(200)
            ->assertJson($expectedTree);
    }

    /** @test */
    public function store_is_updated_correctly()
    {
        //TEST - update store L name

        $store = Store::where('store_name', 'L')->first(); //id => 12, update store L

        $payload = [
            'parent_id' => '', //retain the current 'parent_id' other than moving this store and its children
            'store_name' => 'L_updated' //update the store name
        ];

        $this->json('PUT', '/api/stores/'.$store->id, $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'id' => 12,
                'parent_id' => 7,
                'store_name' => 'L_updated'
            ]);
    }

    /** @test */
    public function store_and_children_are_moved_correctly()
    {
        //TEST - move store A and its children D and E under store C

        $expectedTree = ['updated_tree' => [
            //node B
            [
                'id' => 2, 'parent_id' => 0, 'store_name' => 'B',
                'children' => [
                    [
                        'id' => 6, 'parent_id' => 2, 'store_name' => 'F'
                    ],
                    [
                        'id' => 7, 'parent_id' => 2, 'store_name' => 'G',
                        'children' => [
                            ['id' => 12, 'parent_id' => 7, 'store_name' => 'L']
                        ]
                    ],
                    [
                        'id' => 8, 'parent_id' => 2, 'store_name' => 'H'
                    ]
                ]
            ],
            //node C
            [
                'id' => 3, 'parent_id' => 0, 'store_name' => 'C',
                'children' => [
                    //node A
                    [
                        'id' => 1, 'parent_id' => 3, 'store_name' => 'A',
                        'children' => [
                            [
                                'id' => 4, 'parent_id' => 1, 'store_name' => 'D'
                            ],
                            [
                                'id' => 5, 'parent_id' => 1, 'store_name' => 'E'
                            ]
                        ]
                    ],
                    [
                        'id' => 9, 'parent_id' => 3, 'store_name' => 'I',
                        'children' => [
                            [
                                'id' => 10, 'parent_id' => 9, 'store_name' => 'J'
                            ],
                            [
                                'id' => 11, 'parent_id' => 9, 'store_name' => 'K'
                            ]
                        ]
                    ]
                ]
            ]//END - node c
        ]
        ];

        //id => 1, move store A and its children D and E under store C
        $store = Store::where('store_name', 'A')->first();

        $payload = [
            'parent_id' => '3' //store C will be the new parent of store A and its children
        ];

        $this->json('PUT', '/api/stores/'.$store->id, $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson($expectedTree);
    }

    /** @test */
    public function store_and_children_are_deleted_correctly()
    {
        //TEST - delete a specific store and its children

        $expectedTree = [
            'updated_tree' => [
                //node A
                [
                    'id' => 1, 'parent_id' => 0, 'store_name' => 'A',
                    'children' => [
                        [
                            'id' => 4, 'parent_id' => 1, 'store_name' => 'D'
                        ],
                        [
                            'id' => 5, 'parent_id' => 1, 'store_name' => 'E'
                        ]
                    ]
                ],
                //node C
                [
                    'id' => 3, 'parent_id' => 0, 'store_name' => 'C',
                    'children' => [
                        [
                            'id' => 9, 'parent_id' => 3, 'store_name' => 'I',
                            'children' => [
                                [
                                    'id' => 10, 'parent_id' => 9, 'store_name' => 'J'
                                ],
                                [
                                    'id' => 11, 'parent_id' => 9, 'store_name' => 'K'
                                ]
                            ]
                        ]
                    ]
                ]//END - node c
            ]
        ];
        $store = Store::where('store_name', 'B')->first(); //id => 2, remove store B and its children

        $this->json('DELETE', '/api/stores/'.$store->id, [], $this->headers)
            ->assertStatus(200) //to demonstrate the updated result, return 200 other than 204
            ->assertJson($expectedTree);
    }

    /** @test */
    public function store_is_created_correctly()
    {
        //TEST - create a new store as A's child

        $expectedStore = [
            'new_store' => [
                'id' => 13,
                'parent_id' => 1,
                'store_name' => 'M'
            ]
        ];

        $payload = [
            'parent_id' => 1,
            'store_name' => 'M'
        ];

        $this->json('POST', '/api/stores', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson($expectedStore);
    }
}
