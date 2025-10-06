# MenuBuilder Helper

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/menu_builder?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/menu_builder?label=Packagist)](https://packagist.org/packages/pieceofcake2/menu_builder)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/menu_builder/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/menu_builder/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/menu_builder/CI.yml?label=CI)](https://github.com/pieceofcake2/menu_builder/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/menu_builder?label=Coverage)](https://codecov.io/gh/pieceofcake2/menu_builder)

__This is forked for CakePHP2.__

A dynamic menu building helper for CakePHP

## Background

This is a menu building helper with lot of customization options. Check out the **Usage** section.

Now it supports menus built with [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component) by [Mark Story](http://mark-story.com/)

## Features

* Generate menu based on current user type/group/permission/level (Can be used with Auth, Authsome, etc Components)
* Provide various useful CSS class
* Multi-level menu support
* Supports [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component) by [Mark Story](http://mark-story.com/)
* CakePHP Unit Test (~~100% Code coverage~~)

## Requirements

* CakePHP 2.10+
* PHP 8.0+

## Installation

Add the plugin to your project's:

```bash
composer require pieceofcake2/menu_builder
```

Because this plugin has the type `cakephp-plugin` set in its own `composer.json`, Composer will install it inside your `/Plugins` directory, rather than in the usual vendors file. It is recommended that you add `/Plugins/MenuBuilder` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)

### Enable plugin

In 2.x you need to enable the plugin in your `app/Config/bootstrap.php` file:

```php
CakePlugin::load('MenuBuilder');
```

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

### Minimal Setup
Load the Plugin by modifying your app/Config/bootstrap.php

```php
...
CakePlugin::load('MenuBuilder');
```

or

```php
...
CakePlugin::loadAll();
```

To use this helper add the following to your AppController:

```php
...
var $helpers = [..., 'MenuBuilder.MenuBuilder'];

function beforeFilter() {
    ...
    // Define your menu
    $menu = [
        'main-menu' => [
            [
                'title' => 'Home',
                'url' => ['controller' => 'pages', 'action' => 'home'],
            ],
            [
                'title' => 'About Us',
                'url' => '/pages/about-us',
            ],
        ],
        'left-menu' => [
            [
                'title' => 'Item 1',
                'url' => ['controller' => 'items', 'action' => 'view', 1],
                'children' => [
                    [
                        'title' => 'Item 3',
                        'url' => ['controller' => 'items', 'action' => 'view', 3],
                    ],
                    [
                        'title' => 'Item 4',
                        'url' => ['controller' => 'items', 'action' => 'view', 4],
                    ],
                ]
            ],
            [
                'title' => 'Item 2',
                'url' => ['controller' => 'items', 'action' => 'view', 2],
            ],
        ],
    ];

    // For default settings name must be menu
    $this->set(compact('menu'));
    ...
}
```

Now to build your `main-menu` use the following code in the View:

```php
echo $this->MenuBuilder->build('main-menu');
```

You'll get the following output in the Home (/pages/home) page:

```html
<ul id="main-menu">
    <li class="first-item active"><a title="Home" href="/pages/home">Home</a></li>
    <li><a title="About Us" href="/pages/about-us">About Us</a></li>
</ul>
```

And to build your `left-menu` use the following code in the View:

```php
<?php
    echo $this->MenuBuilder->build('left-menu');
?>
```

You'll get the following output in your 'Item 4' (/items/view/4) page:

```html
<ul id="left-menu">
    <li class="first-item active has-children">
        <a title="Item 1" href="/items/view/1">Item 1</a>
        <ul>
            <li class="first-item">
                <a title="Item 3" href="/items/view/3">Item 3</a>
            </li>
            <li class="active">
                <a title="Item 4" href="/items/view/4">Item 4</a>
            </li>
        </ul>
    </li>
    <li>
        <a title="Item 2" href="/items/view/2">Item 2</a>
    </li>
</ul>
```

You can pass optional parameter in `build` function like -

```php
<?php
    echo $this->MenuBuilder->build('main-menu', ['class' => ['fun', 'red']]);
    // OR
    echo $this->MenuBuilder->build('main-menu', ['class' => 'fun green']);
?>
```

## Advance Setup

You can provide advance options in the array like the following:


```php
...
var $helpers = [
    ...
    'MenuBuilder.MenuBuilder' => [/* array of settings */]
];
```

### Default Settings
if you do not provide any settings then the following settings will work.

```php
$settings = [
    'activeClass' => 'active',
    'firstClass' => 'first-item',
    'childrenClass' => 'has-children',
    'evenOdd' => false,
    'itemFormat' => '<li%s>%s%s</li>',
    'wrapperFormat' => '<ul%s>%s</ul>',
    'noLinkFormat' => '<a href="#">%s</a>',
    'menuVar' => 'menu',
    'authVar' => 'user',
    'authModel' => 'User',
    'authField' => 'group',
];
```

### Settings Details

**activeClass**
CSS classname for the selected/current item and it's successors. *(default - `'active'`)*

**firstClass**
CSS classname for the first item of each level. *(default - `'first-item'`)*

**childrenClass**
CSS classname for an item containing sub menu. *(default - `'has-children'`)*

**evenOdd**
If it is set to `true` then even/odd classname will be provided with each item. *(default - `false`)*

```html
<ul id="main-menu">
    <li class="first-item odd">
        <a title="Home" href="/pages/home">Home</a>
    </li>
    <li class="even">
        <a title="About Us" href="/pages/about-us">About Us</a>
    </li>
</ul>
```

**itemFormat**
if you want to use other tag than `li` for menu items *(default - `'<li%s>%s%s</li>'`)*

**wrapperFormat**
if you want to use other tag than `ul` for menu items container *(default - `'<ul%s>%s</ul>'`)*

**noLinkFormat**
Format for empty link item *(default - `'<a href="#">%s</a>'`)*

*Example Setting*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<div%s>%s%s</div>',
    'wrapperFormat' => '<div%s>%s</div>',
    'noLinkFormat' => '<div>%s</div>',
],
```

*Example Output (an extra item added to explain `noLinkFormat`)*

```html
<div id="main-menu">
    <div class="first-item">
        <a title="Home" href="/pages/home">Home</a>
    </div>
    <div>
        <a title="About Us" href="/pages/about-us">About Us</a>
    </div>
    <div>
        Empty
    </div>
</div>
```

**menuVar**
Name of the variable that contains all menus *(default - `'menu'`)*

*Following settings will be used for permission based menu (see below)*

**authVar**
Name of the variable that contains the `User` data *(default - `'user'`)*

**authModel**
Name of the authentication model *(default - `'User'`)*

**authField**
Name of the field that contains the user's type/group/permission/level *(default - `'group'`)*

### Permission Based Menu

Suppose you have a `users` table like the following one:

```sql
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `password` char(40) NOT NULL,
    `group` enum('user','manager','admin') NOT NULL DEFAULT 'user',
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Now suppose you are using the CakePHP auth component for authentication so add the following to your AppController:

```php
...
function beforeFilter() {
    ...
    $user = $this->Auth->user();
    $this->set(compact('user'));
}
```

But, If you are using [Authsome](https://github.com/felixge/cakephp-authsome) component for authentication then add the following to your AppController:


```php
...
function beforeFilter() {
    ...
    $user = Authsome::get();
    $this->set(compact('user'));
}
```

Now we have to define permissions in our menu like this:

```php
...
function beforeFilter() {
    ...
    // Define your menu
    $menu = [
        'main-menu' => [
            // Anybody can see this
            [
                'title' => 'Home',
                'url' => ['controller' => 'pages', 'action' => 'home'],
            ],
            // Users and Admins can see this, Guests and Managers can't
            [
                'title' => 'About Us',
                'url' => ['controller' => 'pages', 'action' => 'about-us'],
                'permissions' => ['user','admin'],
            ],
            // Only Guests can see this
            [
                'title' => 'Login',
                'url' => ['controller' => 'users', 'action' => 'login'],
                'permissions' => [''],
            ],
        ],
        ...
    ];
    // For default settings name must be menu
    $this->set(compact('menu'));
    ...
}
```

**You're Done!**

### Other Menu Options

**permissions**
Array of type/group/permission/level whose can view that item *(default - `[]`)*

**partialMatch**
Normally `url` matching are strict. Suppose you are in `/items/details` and your menu contains an entry for `/items` then by default it'll not set active. But if you set `partialMatch` to `true` then it'll set active . *(default - `false`)*

**id**
Provide CSS id to the item *(default - `null`)*

**class**
Provide CSS class to the item *(default - `null`)*

**separator**
If you want to define some separator in your menu, below is a nice example of what you can do with it. *(default - `false`)*

*Example Setting*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<dd%s>%s%s</dd>',
    'wrapperFormat' => '<dl%s>%s</dl>',
    'noLinkFormat' => '<dd>%s</dd>',
],
```

*Example Menu*

```php
...
var $helpers = [..., 'MenuBuilder.MenuBuilder'];

function beforeFilter() {
    ...
    // Define your menu
    $menu = [
        'main-menu' => [
            [
                'separator' => '<dt>Main Menu</dt>',
            ],
            [
                'title' => 'Home',
                'url' => ['controller' => 'pages', 'action' => 'home'],
            ],
            [
                'title' => 'About Us',
                'url' => '/pages/about-us',
            ],
        ]
    ];

    // For default settings name must be menu
    $this->set(compact('menu'));
    ...
}
```


*Example Output*

```html
<dl id="main-menu">
    <dt>Main Menu</dt>
    <dd class="first-item">
        <a title="Home" href="/pages/home">Home</a>
    </dd>
    <dd>
        <a title="About Us" href="/pages/about-us">About Us</a>
    </dd>
</dl>
```

**More to come :)**

## ToDo

*Add More Test Cases*

## License

MIT License
