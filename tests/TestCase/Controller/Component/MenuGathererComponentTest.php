<?php

App::uses('MenuGathererComponent', 'MenuBuilder.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeRequest', 'Network');

/**
 * MenuGathererComponent Test Case
 */
class MenuGathererComponentTest extends CakeTestCase
{
    /**
     * SetUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->_admin = Configure::read('Routing.prefixes.0');
        Configure::write('Routing.prefixes.0', 'admin');
        CakeSession::destroy();
        $this->Controller = new TestMenuGathererController(new CakeRequest(), new CakeResponse());
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();

        $this->MenuGatherer = new TestMenuGathererComponent(new ComponentCollection());
    }

    /**
     * TearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->MenuGatherer);

        parent::tearDown();
    }

    /**
     * TestGet method
     *
     * @return void
     */
    public function testGet(): void
    {
        $result = $this->MenuGatherer->get();
        $expected = [];
        $this->assertEquals($expected, $result);
    }

    /**
     * Getting a menu that does not exist returns null without a warning
     *
     * @return void
     */
    public function testGetUnknownMenu(): void
    {
        $this->MenuGatherer->item('main', ['title' => 'Home', 'url' => '/']);

        $this->assertNull($this->MenuGatherer->get('unknown'));
        $this->assertSame(['main'], array_keys($this->MenuGatherer->get()));
    }

    /**
     * TestItem method
     *
     * @return void
     */
    public function testItem(): void
    {
        $this->MenuGatherer->item('main', ['item1' => ['controller' => 'pages', 'action' => 'display', 'item1']]);
        $result = $this->MenuGatherer->get('main');
        $expected = [['item1' => ['controller' => 'pages', 'action' => 'display', 'item1']]]; //?
        $this->assertEquals($expected, $result);
    }

    /**
     * Adding an item to a new menu must not touch the other menus
     *
     * @return void
     */
    public function testItemKeepsOtherMenus(): void
    {
        $footer = [['title' => 'Contact', 'url' => '/contact']];
        $this->MenuGatherer->menu('footer', $footer);

        $home = ['title' => 'Home', 'url' => '/'];
        $this->MenuGatherer->item('main', $home);

        $expected = [
            'footer' => $footer,
            'main' => [$home],
        ];
        $this->assertSame($expected, $this->MenuGatherer->get());
    }

    /**
     * Adding items to several new menus keeps each of them
     *
     * @return void
     */
    public function testItemCreatesMenusIndependently(): void
    {
        $home = ['title' => 'Home', 'url' => '/'];
        $contact = ['title' => 'Contact', 'url' => '/contact'];
        $this->MenuGatherer->item('main', $home);
        $this->MenuGatherer->item('footer', $contact);

        $expected = [
            'main' => [$home],
            'footer' => [$contact],
        ];
        $this->assertSame($expected, $this->MenuGatherer->get());
    }

    /**
     * TestItem method with an index
     *
     * @return void
     */
    public function testItemWithIndex(): void
    {
        $footer = [['title' => 'Contact', 'url' => '/contact']];
        $this->MenuGatherer->menu('footer', $footer);

        $first = ['title' => 'First', 'url' => '/first'];
        $second = ['title' => 'Second', 'url' => '/second'];
        $third = ['title' => 'Third', 'url' => '/third'];
        $last = ['title' => 'Last', 'url' => '/last'];

        $this->MenuGatherer->item('main', $first);
        $this->MenuGatherer->item('main', $third);
        $this->MenuGatherer->item('main', $second, 1);
        $this->MenuGatherer->item('main', $last, 3);

        $this->assertSame([$first, $second, $third, $last], $this->MenuGatherer->get('main'));
        $this->assertSame($footer, $this->MenuGatherer->get('footer'));
    }

    /**
     * TestItem method with an index on a menu that does not exist yet
     *
     * @return void
     */
    public function testItemWithIndexOnNewMenu(): void
    {
        $home = ['title' => 'Home', 'url' => '/'];
        $this->MenuGatherer->item('main', $home, 0);

        $this->assertSame(['main' => [$home]], $this->MenuGatherer->get());
    }

    /**
     * TestItem method with a negative index
     *
     * @return void
     */
    public function testItemWithNegativeIndex(): void
    {
        $first = ['title' => 'First', 'url' => '/first'];
        $second = ['title' => 'Second', 'url' => '/second'];
        $last = ['title' => 'Last', 'url' => '/last'];

        $this->MenuGatherer->menu('main', [$first, $last]);
        $this->MenuGatherer->item('main', $second, -1);

        $this->assertSame([$first, $second, $last], $this->MenuGatherer->get('main'));
    }

    /**
     * TestMenu method
     *
     * @return void
     */
    public function testMenu(): void
    {
        $this->MenuGatherer->menu('smasher', [
            [
                'separator' => '<dt>smasher</dt>',
            ],
            [
                'title' => 'Home',
                'url' => ['controller' => 'pages', 'action' => 'home'],
            ],
            [
                'title' => 'About Me - I am a Smashing Menu system',
                'url' => '/pages/about-menu-builder',
            ],
            [
                'title' => 'Contact the Menu Builder',
                'url' => '/contact',
            ],
        ]);
        $expected = $this->MenuGatherer->get('smasher');
        $result = [
            [
                'separator' => '<dt>smasher</dt>',
            ],
            [
                'title' => 'Home',
                'url' => ['controller' => 'pages', 'action' => 'home'],
            ],
            [
                'title' => 'About Me - I am a Smashing Menu system',
                'url' => '/pages/about-menu-builder',
            ],
            [
                'title' => 'Contact the Menu Builder',
                'url' => '/contact',
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * TestMenu method with an array of menus
     *
     * @return void
     */
    public function testMenuWithArray(): void
    {
        $main = [['title' => 'Home', 'url' => '/']];
        $footer = [['title' => 'Contact', 'url' => '/contact']];
        $this->MenuGatherer->menu(['main' => $main, 'footer' => $footer]);

        $this->assertEquals($main, $this->MenuGatherer->get('main'));
        $this->assertEquals($footer, $this->MenuGatherer->get('footer'));
    }

    /**
     * TestSet method
     *
     * @return void
     */
    public function testSet(): void
    {
        $this->MenuGatherer->set(['item1' => ['controller' => 'pages', 'action' => 'display', 'item1'], 'item1' => ['controller' => 'pages', 'action' => 'display', 'item1']]);
        $expected = $this->MenuGatherer->get();
        $result = ['item1' => ['controller' => 'pages', 'action' => 'display', 'item1']];
        $this->assertEquals($expected, $result);
    }
}
