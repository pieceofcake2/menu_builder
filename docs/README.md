# MenuBuilder Documentation

A dynamic menu building helper for CakePHP 2 with a lot of customization options.

It supports menus built with the [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component)
by [Mark Story](http://mark-story.com/).

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Settings](#settings)
- [Permission Based Menu](#permission-based-menu)
- [Menu Item Options](#menu-item-options)

## Features

* Generates menus based on the current user's type/group/permission/level (can be used with
  the Auth component, Authsome and others)
* Provides various useful CSS classes
* Multi-level menu support
* Supports the [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component) by
  [Mark Story](http://mark-story.com/)

## Installation

Install the plugin with [Composer](https://getcomposer.org/):

```bash
composer require pieceofcake2/menu_builder
```

The plugin has the `cakephp-plugin` type, so it is installed into your plugins directory
(as `MenuBuilder`) by `composer/installers`. Set `installer-paths` in your application's
`composer.json` so it is installed into the right place:

| Layout | `installer-paths` |
| --- | --- |
| Modern (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| Legacy (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

It is recommended that you add the installed plugin directory (e.g. `plugins/MenuBuilder`) to
your `.gitignore` file.

### Enable the plugin

Load the plugin in `config/bootstrap.php` (legacy layout: `app/Config/bootstrap.php`).
If you already use `CakePlugin::loadAll()`, this is not necessary:

```php
CakePlugin::load('MenuBuilder');
```

## Usage

To use this helper, add the following to your `AppController`:

```php
class AppController extends Controller
{
    public $helpers = ['MenuBuilder.MenuBuilder'];

    public function beforeFilter()
    {
        parent::beforeFilter();

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
                    ],
                ],
                [
                    'title' => 'Item 2',
                    'url' => ['controller' => 'items', 'action' => 'view', 2],
                ],
            ],
        ];

        // With the default settings, the variable must be named menu
        $this->set(compact('menu'));
    }
}
```

To build your `main-menu`, use the following code in the view:

```php
echo $this->MenuBuilder->build('main-menu');
```

You get the following output on the Home (`/pages/home`) page:

```html
<ul class="main-menu" id="main-menu">
    <li class="first-item active"><a title="Home" href="/pages/home">Home</a></li>
    <li><a title="About Us" href="/pages/about-us">About Us</a></li>
</ul>
```

To build your `left-menu`, use the following code in the view:

```php
echo $this->MenuBuilder->build('left-menu');
```

You get the following output on the Item 4 (`/items/view/4`) page:

```html
<ul class="left-menu" id="left-menu">
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

The top level wrapper gets the menu name as its id and class. You can add CSS classes with the
`class` option of `build()`:

```php
// <ul class="main-menu fun green" id="main-menu">
echo $this->MenuBuilder->build('main-menu', ['class' => 'fun green']);
```

The other options of `build()` are [settings](#settings); they override the helper settings
from then on.

## Settings

You can pass settings to the helper:

```php
public $helpers = [
    'MenuBuilder.MenuBuilder' => [/* array of settings */],
];
```

### Default settings

If you do not provide any settings, the following defaults are used:

```php
public $helpers = [
    'MenuBuilder.MenuBuilder' => [
        'activeClass' => 'active',
        'firstClass' => 'first-item',
        'childrenClass' => 'has-children',
        'menuClass' => null,
        'evenOdd' => false,
        'itemFormat' => '<li%s>%s%s</li>',
        'wrapperFormat' => '<ul%s>%s</ul>',
        'wrapperClass' => null,
        'noLinkFormat' => '<a href="#">%s</a>',
        'menuVar' => 'menu',
        'authVar' => 'user',
        'authModel' => 'User',
        'authField' => 'group',
        'indentHtmlOutput' => true,
    ],
];
```

### Settings details

**activeClass**
CSS class name for the selected/current item and its ancestors. *(default `'active'`)*

**firstClass**
CSS class name for the first item of each level. *(default `'first-item'`)*

**childrenClass**
CSS class name for an item containing a sub menu. *(default `'has-children'`)*

**menuClass**
CSS class name for the top level wrapper (`ul`). *(default `null`)*

**wrapperClass**
CSS class name for the wrappers of sub menus. *(default `null`)*

**evenOdd**
If set to `true`, an even/odd class name is added to each item. *(default `false`)*

```html
<ul class="main-menu" id="main-menu">
    <li class="first-item odd">
        <a title="Home" href="/pages/home">Home</a>
    </li>
    <li class="even">
        <a title="About Us" href="/pages/about-us">About Us</a>
    </li>
</ul>
```

**itemFormat**
Format of the menu items, if you want to use another tag than `li`. *(default
`'<li%s>%s%s</li>'`)*

**wrapperFormat**
Format of the menu item containers, if you want to use another tag than `ul`. *(default
`'<ul%s>%s</ul>'`)*

**noLinkFormat**
Format of items without a URL. *(default `'<a href="#">%s</a>'`)*

*Example settings*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<div%s>%s%s</div>',
    'wrapperFormat' => '<div%s>%s</div>',
    'noLinkFormat' => '<div>%s</div>',
],
```

*Example output (an extra item without URL added to explain `noLinkFormat`)*

```html
<div class="main-menu" id="main-menu">
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
Name of the view variable that contains all menus. *(default `'menu'`)*

**indentHtmlOutput**
Indent the generated HTML. *(default `true`)*

**defaults**
Default [options of each menu item](#menu-item-options), merged into every item.

The following settings are used for permission based menus (see below):

**authVar**
Name of the view variable that contains the user data. *(default `'user'`)*

**authModel**
Name of the authentication model. *(default `'User'`)*

**authField**
Name of the field that contains the user's type/group/permission/level. *(default `'group'`)*

## Permission Based Menu

Suppose you have a `users` table like the following one:

```sql
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `group` enum('user','manager','admin') NOT NULL DEFAULT 'user',
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

The helper reads the group from `$user['User']['group']` (see `authVar`, `authModel` and
`authField`). If you use the Auth component for authentication, add the following to your
`AppController`:

```php
public function beforeFilter()
{
    parent::beforeFilter();

    $user = ['User' => $this->Auth->user()];
    $this->set(compact('user'));
}
```

If you use [Authsome](https://github.com/felixge/cakephp-authsome) for authentication, add the
following instead:

```php
public function beforeFilter()
{
    parent::beforeFilter();

    $user = Authsome::get();
    $this->set(compact('user'));
}
```

Now define the permissions in your menu:

```php
$menu = [
    'main-menu' => [
        // Anybody can see this
        [
            'title' => 'Home',
            'url' => ['controller' => 'pages', 'action' => 'home'],
        ],
        // Users and admins can see this, guests and managers can't
        [
            'title' => 'About Us',
            'url' => ['controller' => 'pages', 'action' => 'about-us'],
            'permissions' => ['user', 'admin'],
        ],
        // Only guests can see this
        [
            'title' => 'Login',
            'url' => ['controller' => 'users', 'action' => 'login'],
            'permissions' => [''],
        ],
    ],
];

// With the default settings, the variable must be named menu
$this->set(compact('menu'));
```

## Menu Item Options

**title**
Title of the item. Items without a title (and without `separator`) are not rendered.

**url**
URL of the item, as a string or a URL array. Items without a URL use `noLinkFormat`.

**children**
Array of sub menu items.

**permissions**
Array of types/groups/permissions/levels that can view the item. *(default `[]`, everybody)*

**partialMatch**
Normally URL matching is strict: if you are on `/items/details` and your menu contains an entry
for `/items`, it is not set active by default. If you set `partialMatch` to `true`, it is set
active. *(default `false`)*

**id**
CSS id of the item. *(default `null`)*

**class**
CSS class of the item, as a string or an array. *(default `null`)*

**linkClass**
CSS class of the link. *(default `null`)*

**target**
`target` attribute of the link. *(default `null`)*

**escape**, **escapeTitle**
Passed to `HtmlHelper::link()`.

**image**
Image shown in the link, followed by the title in a `span` with the `label` class.
*(default `null`)*

**separator**
Renders the given HTML instead of an item. Below is an example of what you can do with it.
*(default `false`)*

*Example settings*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<dd%s>%s%s</dd>',
    'wrapperFormat' => '<dl%s>%s</dl>',
    'noLinkFormat' => '<dd>%s</dd>',
],
```

*Example menu*

```php
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
    ],
];

// With the default settings, the variable must be named menu
$this->set(compact('menu'));
```

*Example output*

```html
<dl class="main-menu" id="main-menu">
    <dt>Main Menu</dt>
    <dd class="first-item">
        <a title="Home" href="/pages/home">Home</a>
    </dd>
    <dd>
        <a title="About Us" href="/pages/about-us">About Us</a>
    </dd>
</dl>
```
