<?php

App::uses('MenuGathererComponent', 'MenuBuilder.Controller/Component');

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
