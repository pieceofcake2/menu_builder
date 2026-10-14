<?php
App::uses('Component', 'Controller');

class MenuGathererComponent extends Component
{
    protected $_controller;

    protected $_menu = [];

    /**
     * Initialize component
     *
     * @param Controller $controller Instantiating controller
     * @return void
     */
    public function initialize(Controller $controller)
    {
        parent::initialize($controller);

        $this->_controller = $controller;
    }

    /**
     * Get a menu, or all menus
     *
     * @param string|null $menu Menu name, or null for all menus
     * @return array|null Menu data, or null when the menu does not exist
     */
    public function get($menu = null)
    {
        if ($menu === null) {
            return $this->_menu;
        }

        return $this->_menu[$menu] ?? null;
    }

    /**
     * Add an item to a menu
     *
     * The menu is created if it does not exist yet. Without `$index` the item
     * is appended; otherwise it is inserted at that position (as with
     * `array_splice()`, a negative index counts from the end of the menu).
     *
     * @param string $menu Menu name
     * @param array $item Item
     * @param int|null $index Position to insert the item at, or null to append it
     * @return void
     */
    public function item($menu, $item = [], $index = null)
    {
        $this->_checkMenu($menu);

        if ($index === null) {
            $this->_menu[$menu][] = $item;

            return;
        }

        array_splice($this->_menu[$menu], $index, 0, [$item]);
    }

    /**
     * MenuGathererComponent::menu()
     *
     * @param mixed $name Name
     * @param mixed $menu Menu
     * @return void
     */
    public function menu($name, $menu = [])
    {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->menu($key, $val);
            }

            return;
        }

        $this->_menu[$name] = $menu;
    }

    /**
     * MenuGathererComponent::set()
     *
     * @param mixed $menu Menu
     * @return void
     */
    public function set($menu = [])
    {
        $this->_menu = (array)$menu;
    }

    /**
     * Initialise the given menu(s) as empty if they do not exist yet
     *
     * Existing menus are left untouched.
     *
     * @param array|string $name Menu name or list of menu names
     * @return void
     */
    protected function _checkMenu($name)
    {
        if (is_array($name)) {
            foreach ($name as $val) {
                $this->_checkMenu($val);
            }

            return;
        }

        if (!isset($this->_menu[$name])) {
            $this->_menu[$name] = [];
        }
    }
}
