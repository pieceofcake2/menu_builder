<?php

App::uses('MenuGathererComponent', 'MenuBuilder.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeRequest', 'Network');

/**
 * MenuGathererComponent Test Case
 */
class MenuGathererComponentTestCase extends CakeTestCase
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

class TestMenuGathererComponent extends MenuGathererComponent
{
    public $name = 'MenuGatherer';

    public $cacheKey = 'test_menu_storage';

    /**
     * TestMenuGathererComponent::getMenu()
     *
     * @return array
     */
    public function getMenu(): array
    {
        return $this->_menu;
    }
}

class TestMenuGathererController extends Controller
{
    public $components = ['MenuBuilder.TestMenuGatherer'];
}

class AuthUser extends CakeTestModel
{
    public $name = 'AuthUser';
}

class Controller1Controller extends Controller
{
    /**
     * Controller1Controller::action1()
     *
     * @return void
     */
    public function action1(): void
    {
    }

    /**
     * Controller1Controller::action2()
     *
     * @return void
     */
    public function action2(): void
    {
    }
}

class Controller2Controller extends Controller
{
    /**
     * Controller2Controller::action1()
     *
     * @return void
     */
    public function action1(): void
    {
    }

    /**
     * Controller2Controller::action2()
     *
     * @return void
     */
    public function action2(): void
    {
    }

    /**
     * Controller2Controller::admin_action()
     *
     * @return void
     */
    public function admin_action(): void // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
    }
}
