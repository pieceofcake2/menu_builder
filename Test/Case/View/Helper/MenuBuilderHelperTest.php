<?php
App::uses('MenuBuilderHelper', 'MenuBuilder.View/Helper');
App::uses('Controller', 'Controller');
App::uses('View', 'View');

class MenuBuilderHelperTest extends CakeTestCase
{
    /**
     * Start Test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
            ],
        ];

        $guest = [
            'User' => [
                'group' => '',
            ],
        ];

        $user = [
            'User' => [
                'group' => 'user',
            ],
        ];

        $admin = [
            'User' => [
                'group' => 'admin',
            ],
        ];

        Configure::delete('Routing.prefixes');

        $this->Controller = new Controller();
        $this->Controller->set(compact('menu'));
        $this->Controller->set(compact('guest'));
        $this->Controller->set(compact('user'));
        $this->Controller->set(compact('admin'));
        $this->View = new View($this->Controller);
        $this->View->request = new CakeRequest(null, false);
        $this->MenuBuilder = new MenuBuilderHelper($this->View);
    }

    /**
     * End Test
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->MenuBuilder, $this->view);
        parent::tearDown();
    }

    /**
     * TestBuildDefault Default build test
     *
     * @return void
     */
    public function testBuildDefault(): void
    {
        $result = $this->MenuBuilder->build();
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestNoLink Menu with no URL
     *
     * @return void
     */
    public function testNoLink(): void
    {
        // Normal Menu
        $menu = [
            [
                'title' => 'Item 1',
            ],
            [
                'title' => 'Item 2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '#']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With One One Level Sub Menu
        $menu[0]['children'] = [
            [
                'title' => 'Item 1.1',
            ],
            [
                'title' => 'Item 1.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '#']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '#']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                '<li', ['a' => ['href' => '#']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Two One Level Sub Menu
        $menu[1]['children'] = [
            [
                'title' => 'Item 2.1',
            ],
            [
                'title' => 'Item 2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '#']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '#']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '#']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '#']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Multi Level Sub Menu
        $menu[0]['children'][1]['children'] = [
            [
                'title' => 'Item 1.2.1',
            ],
            [
                'title' => 'Item 1.2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '#']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'has-children']],
                            ['a' => ['href' => '#']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 1.2.1', '</a', '</li',
                                '<li', ['a' => ['href' => '#']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '#']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '#']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '#']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestWithLink Menu with URL
     *
     * @return void
     */
    public function testWithLink(): void
    {
        // Normal Menu
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With One One Level Sub Menu
        $menu[0]['children'] = [
            [
                'title' => 'Item 1.1',
                'url' => '/item-1.1',
            ],
            [
                'title' => 'Item 1.2',
                'url' => '/item-1.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Two One Level Sub Menu
        $menu[1]['children'] = [
            [
                'title' => 'Item 2.1',
                'url' => '/item-2.1',
            ],
            [
                'title' => 'Item 2.2',
                'url' => '/item-2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Multi Level Sub Menu
        $menu[0]['children'][1]['children'] = [
            [
                'title' => 'Item 1.2.1',
                'url' => '/item-1.2.1',
            ],
            [
                'title' => 'Item 1.2.2',
                'url' => '/item-1.2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                '<li', ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestActiveClass Current Page Active class check
     *
     * @return void
     */
    public function testActiveClass(): void
    {
        // Normal Menu
        $this->MenuBuilder->here = '/item-1';
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item active']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With One One Level Sub Menu
        $this->MenuBuilder->here = '/item-1.2';
        $menu[0]['children'] = [
            [
                'title' => 'Item 1.1',
                'url' => '/item-1.1',
            ],
            [
                'title' => 'Item 1.2',
                'url' => '/item-1.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item active has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'active']], ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Two One Level Sub Menu
        $this->MenuBuilder->here = '/item-2.1';
        $menu[1]['children'] = [
            [
                'title' => 'Item 2.1',
                'url' => '/item-2.1',
            ],
            [
                'title' => 'Item 2.2',
                'url' => '/item-2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'active has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item active']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Multi Level Sub Menu
        $this->MenuBuilder->here = '/item-1.2.2';
        $menu[0]['children'][1]['children'] = [
            [
                'title' => 'Item 1.2.1',
                'url' => '/item-1.2.1',
            ],
            [
                'title' => 'Item 1.2.2',
                'url' => '/item-1.2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item active has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'active has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                ['li' => ['class' => 'active']], ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestId Test Id
     *
     * @return void
     */
    public function testId(): void
    {
        // Normal Menu
        $menu = [
            [
                'title' => 'Item 1',
                'id' => 'item-1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['id' => 'item-1', 'class' => 'first-item']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With One One Level Sub Menu
        unset($menu[0]['id']);
        $menu[1]['id'] = 'item-2';
        $menu[0]['children'] = [
            [
                'title' => 'Item 1.1',
                'url' => '/item-1.1',
            ],
            [
                'title' => 'Item 1.2',
                'url' => '/item-1.2',
                'id' => 'item-1.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['id' => 'item-1.2']], ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                ['li' => ['id' => 'item-2']], ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Two One Level Sub Menu
        $menu[1]['children'] = [
            [
                'title' => 'Item 2.1',
                'url' => '/item-2.1',
                'id' => 'item-2.1',
            ],
            [
                'title' => 'Item 2.2',
                'url' => '/item-2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['id' => 'item-1.2']], ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                ['li' => ['id' => 'item-2', 'class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['id' => 'item-2.1', 'class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Multi Level Sub Menu
        $menu[0]['children'][1]['children'] = [
            [
                'title' => 'Item 1.2.1',
                'url' => '/item-1.2.1',
            ],
            [
                'title' => 'Item 1.2.2',
                'url' => '/item-1.2.2',
                'id' => 'item-1.2.2',
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['id' => 'item-1.2', 'class' => 'has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                ['li' => ['id' => 'item-1.2.2']], ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['id' => 'item-2', 'class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['id' => 'item-2.1', 'class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestClass Test Class
     *
     * @return void
     */
    public function testClass(): void
    {
        // With Multi Level Sub Menu
        $this->MenuBuilder->here = '/item-1.2';
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
                'class' => ['one', 'two'],
                'children' => [
                    [
                        'title' => 'Item 1.1',
                        'url' => '/item-1.1',
                        'class' => ['three'],
                    ],
                    [
                        'title' => 'Item 1.2',
                        'url' => '/item-1.2',
                        'children' => [
                            [
                                'title' => 'Item 1.2.1',
                                'url' => '/item-1.2.1',
                            ],
                            [
                                'title' => 'Item 1.2.2',
                                'url' => '/item-1.2.2',
                                'class' => 'four',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
                'children' => [
                    [
                        'title' => 'Item 2.1',
                        'url' => '/item-2.1',
                        'class' => ['five', 'six', 'seven'],
                    ],
                    [
                        'title' => 'Item 2.2',
                        'url' => '/item-2.2',
                    ],
                ],
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item active has-children one two']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item three']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'active has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                ['li' => ['class' => 'four']], ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item five six seven']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestWithLink Menu with URL
     *
     * @return void
     */
    public function testMultipleMenu(): void
    {
        // Normal Menu
        $menu = [
            'first-menu' => [
                [
                    'title' => 'Item 1',
                    'url' => '/item-1',
                ],
                [
                    'title' => 'Item 2',
                    'url' => '/item-2',
                ],
            ],
            'second-menu' => [
                [
                    'title' => 'Item 1',
                    'url' => '/item-1',
                    'children' => [
                        [
                            'title' => 'Item 1.1',
                            'url' => '/item-1.1',
                        ],
                        [
                            'title' => 'Item 1.2',
                            'url' => '/item-1.2',
                        ],
                    ],
                ],
                [
                    'title' => 'Item 2',
                    'url' => '/item-2',
                ],
            ],
        ];

        $result = $this->MenuBuilder->build('first-menu', [], $menu);
        $expected = [
            ['ul' => ['class' => 'first-menu', 'id' => 'first-menu']],
                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        $result = $this->MenuBuilder->build('second-menu', [], $menu);
        $expected = [
            ['ul' => ['class' => 'second-menu', 'id' => 'second-menu']],
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a', '</li',
                    '</ul',
                '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestPartialMatch Test Partial URL matching
     *
     * @return void
     */
    public function testPartialMatch(): void
    {
        // With Multi Level Sub Menu
        $this->MenuBuilder->here = '/item-1.2/1.2.3';
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
                'children' => [
                    [
                        'title' => 'Item 1.1',
                        'url' => '/item-1.1',
                    ],
                    [
                        'title' => 'Item 1.2',
                        'url' => '/item-1.2',
                        'partialMatch' => true,
                        'children' => [
                            [
                                'title' => 'Item 1.2.1',
                                'url' => '/item-1.2.1',
                            ],
                            [
                                'title' => 'Item 1.2.2',
                                'url' => '/item-1.2.2',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
                'children' => [
                    [
                        'title' => 'Item 2.1',
                        'url' => '/item-2.1',
                        'partialMatch' => true,
                    ],
                    [
                        'title' => 'Item 2.2',
                        'url' => '/item-2.2',
                    ],
                ],
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item active has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'active has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                '<li', ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                        '<li', ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * TestPermissions Test URL permission
     *
     * @return void
     */
    public function testPermissions(): void
    {
        // With Multi Level Sub Menu
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
                'children' => [
                    [
                        'title' => 'Item 1.1',
                        'url' => '/item-1.1',
                        'permissions' => ['user'],
                    ],
                    [
                        'title' => 'Item 1.2',
                        'url' => '/item-1.2',
                        'permissions' => ['user', 'admin'],
                        'children' => [
                            [
                                'title' => 'Item 1.2.1',
                                'url' => '/item-1.2.1',
                            ],
                            [
                                'title' => 'Item 1.2.2',
                                'url' => '/item-1.2.2',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
                'children' => [
                    [
                        'title' => 'Item 2.1',
                        'url' => '/item-2.1',
                        'permissions' => [''],
                    ],
                    [
                        'title' => 'Item 2.2',
                        'url' => '/item-2.2',
                        'permissions' => ['admin'],
                    ],
                ],
            ],
        ];

        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.1', 'title' => 'Item 1.1']], 'Item 1.1', '</a', '</li',
                        ['li' => ['class' => 'has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                '<li', ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                '<li',
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        $this->MenuBuilder = new MenuBuilderHelper($this->View, ['authVar' => 'admin']);
        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item has-children']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item has-children']],
                            ['a' => ['href' => '/item-1.2', 'title' => 'Item 1.2']], 'Item 1.2', '</a',
                            '<ul',
                                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1.2.1', 'title' => 'Item 1.2.1']], 'Item 1.2.1', '</a', '</li',
                                '<li', ['a' => ['href' => '/item-1.2.2', 'title' => 'Item 1.2.2']], 'Item 1.2.2', '</a', '</li',
                            '</ul',
                        '</li',
                    '</ul',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.2', 'title' => 'Item 2.2']], 'Item 2.2', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        $this->MenuBuilder = new MenuBuilderHelper($this->View, ['authVar' => 'guest']);
        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item']],
                    ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a',
                '</li',
                ['li' => ['class' => 'has-children']],
                    ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2', '</a',
                    '<ul',
                        ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-2.1', 'title' => 'Item 2.1']], 'Item 2.1', '</a', '</li',
                    '</ul',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * testBootstrapMenu Test Bootstrap Themed Menu
     *
     * @return void
     */
    public function testBootstrapMenu(): void
    {
        //no children
        $options = [
            'childrenClass' => 'has-children',
            'menuClass' => 'dashboard-menu',
            'wrapperClass' => 'submenu',
            'noLinkFormat' => '<a class="dropdown-toggle" href="#"><i class="fa fa-cog"></i><span>%s</span><i class="fa fa-chevron-down"></i></a>',
        ];
        $result = $this->MenuBuilder->build('user', $options);
        $expected = [
            'ul' => ['class' => 'user dashboard-menu', 'id' => 'user'],
                'li' => ['class' => 'first-item'],
                ['a' => ['title' => 'Item 1', 'href' => '/item-1']], 'Item 1', '</a',
                '</li',
                '<li',
                ['a' => ['title' => 'Item 2', 'href' => '/item-2']], 'Item 2', '</a',
                '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);

        // With Multi Level Sub Menu
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
                'children' => [
                    [
                        'title' => 'Item 1.1',
                        'url' => '/item-1.1',
                        'permissions' => ['user'],
                    ],
                    [
                        'title' => 'Item 1.2',
                        'url' => '/item-1.2',
                        'permissions' => ['user', 'admin'],
                        'children' => [
                            [
                                'title' => 'Item 1.2.1',
                                'url' => '/item-1.2.1',
                            ],
                            [
                                'title' => 'Item 1.2.2',
                                'url' => '/item-1.2.2',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
                'children' => [
                    [
                        'title' => 'Item 2.1',
                        'url' => '/item-2.1',
                        'permissions' => [''],
                    ],
                    [
                        'title' => 'Item 2.2',
                        'url' => '/item-2.2',
                        'permissions' => ['admin'],
                    ],
                ],
            ],
        ];
        $result = $this->MenuBuilder->build('test', $options, $menu);
        $expected = [
            ['ul' => ['class' => 'test dashboard-menu', 'id' => 'test']],
            ['li' => ['class' => 'first-item has-children']],
                ['a' => ['title' => 'Item 1', 'href' => '/item-1']], 'Item 1', '</a',
            ['ul' => ['class' => 'submenu']],
            ['li' => ['class' => 'first-item']],
                ['a' => ['title' => 'Item 1.1', 'href' => '/item-1.1']], 'Item 1.1', '</a',
            '</li',
            ['li' => ['class' => 'has-children']],
                ['a' => ['title' => 'Item 1.2', 'href' => '/item-1.2']], 'Item 1.2', '</a',
            ['ul' => ['class' => 'submenu']],
            ['li' => ['class' => 'first-item']],
                ['a' => ['title' => 'Item 1.2.1', 'href' => '/item-1.2.1']], 'Item 1.2.1', '</a',
            '</li',
            '<li',
                ['a' => ['title' => 'Item 1.2.2', 'href' => '/item-1.2.2']], 'Item 1.2.2', '</a',
            '</li',
            '</ul',
            '</li',
            '</ul',
            '</li',
            '<li',
            ['a' => ['title' => 'Item 2', 'href' => '/item-2']], 'Item 2', '</a',
            '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * Test that target attribute works.
     *
     * @return void
     */
    public function testMenuWithTargetLinks(): void
    {
        $options = [
            'menuClass' => 'dashboard-menu',
        ];
        // With Multi Level Sub Menu
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
                'target' => '_blank',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
            ],
        ];
        $result = $this->MenuBuilder->build('test', $options, $menu);
        $expected = [
            ['ul' => ['class' => 'test dashboard-menu', 'id' => 'test']],
            ['li' => ['class' => 'first-item']],
            ['a' => ['title' => 'Item 1', 'href' => '/item-1', 'target' => '_blank']], 'Item 1', '</a',
            '</li',
            '<li',
            ['a' => ['title' => 'Item 2', 'href' => '/item-2']], 'Item 2', '</a',
            '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * testImageMenu Test Images in Menu
     *
     * @return void
     */
    public function testImageMenu(): void
    {
        // Necessary hack to prevent CakePHP from calculating wrong webroot dir for CLI testing on travis.
        $this->MenuBuilder->request->webroot = '/';

        $options = [
            'menuClass' => 'dashboard-menu',
        ];
        // With Multi Level Sub Menu
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2',
                'url' => '/item-2',
                'image' => '/path/my-image.jpg',
            ],
        ];
        $result = $this->MenuBuilder->build('test', $options, $menu);
        $expected = [
            ['ul' => ['class' => 'test dashboard-menu', 'id' => 'test']],
            ['li' => ['class' => 'first-item']],
            ['a' => ['title' => 'Item 1', 'href' => '/item-1']], 'Item 1', '</a',
            '</li',
            '<li',
            ['a' => ['title' => 'Item 2', 'href' => '/item-2']],
            ['img' => ['src' => '/path/my-image.jpg', 'alt' => 'Item 2']],
            ['span' => ['class' => 'label']],
            'Item 2',
            '</span',
            '</a',
            '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }

    /**
     * testSanitizeOfLinkTitle Test clearing link title attribute of undesirable characters
     *
     * @return void
     */
    public function testSanitizeOfLinkTitle(): void
    {
        $menu = [
            [
                'title' => 'Item 1',
                'url' => '/item-1',
            ],
            [
                'title' => 'Item 2&nbsp;<i class="fa fa-caret-right"></i>',
                'url' => '/item-2',
            ],
        ];
        $result = $this->MenuBuilder->build(null, [], $menu);
        $expected = [
            '<ul',
                ['li' => ['class' => 'first-item']], ['a' => ['href' => '/item-1', 'title' => 'Item 1']], 'Item 1', '</a', '</li',
                '<li', ['a' => ['href' => '/item-2', 'title' => 'Item 2']], 'Item 2&amp;nbsp;&lt;i class=&quot;fa fa-caret-right&quot;&gt;&lt;/i&gt;', '</a', '</li',
            '</ul',
        ];
        $this->assertTags($result, $expected, true);
    }
}
