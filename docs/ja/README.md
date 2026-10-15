# MenuBuilder ドキュメント

カスタマイズのオプションが豊富な、CakePHP 2 用の動的なメニュー作成ヘルパーです。

[Mark Story](http://mark-story.com/) による [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component)
で作ったメニューにも対応しています。

- [特徴](#features)
- [インストール](#installation)
- [使い方](#usage)
- [設定](#settings)
- [権限に応じたメニュー](#permission-based-menu)
- [メニュー項目のオプション](#menu-item-options)

<a id="features"></a>

## 特徴

* 現在のユーザーの種別・グループ・権限・レベルに応じてメニューを生成します (Auth コンポーネント、
  Authsome などと一緒に使えます)
* 便利な CSS クラスをいろいろ付けます
* 多階層のメニューに対応しています
* [Mark Story](http://mark-story.com/) による
  [ACL Menu Component](http://mark-story.com/posts/view/acl-menu-component) に対応しています

<a id="installation"></a>

## インストール

[Composer](https://getcomposer.org/) でプラグインをインストールします。

```bash
composer require pieceofcake2/menu_builder
```

このプラグインのタイプは `cakephp-plugin` なので、`composer/installers` によってプラグインディレクトリに
(`MenuBuilder` として) インストールされます。正しい場所にインストールされるように、アプリケーションの
`composer.json` で `installer-paths` を設定してください。

| レイアウト | `installer-paths` |
| --- | --- |
| モダン (`config/`、`src/`、`templates/`、`plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| レガシー (`app/Config/`、`app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

インストールされたプラグインのディレクトリ (たとえば `plugins/MenuBuilder`) は、`.gitignore` ファイルに
追加することをお勧めします。

<a id="enable-the-plugin"></a>

### プラグインの有効化

`config/bootstrap.php` (レガシーレイアウトでは `app/Config/bootstrap.php`) でプラグインを読み込みます。
すでに `CakePlugin::loadAll()` を使っている場合は不要です。

```php
CakePlugin::load('MenuBuilder');
```

<a id="usage"></a>

## 使い方

このヘルパーを使うには、`AppController` に次のように追加します。

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

`main-menu` を作るには、ビューで次のコードを使います。

```php
echo $this->MenuBuilder->build('main-menu');
```

Home (`/pages/home`) ページでは、次のように出力されます。

```html
<ul class="main-menu" id="main-menu">
    <li class="first-item active"><a title="Home" href="/pages/home">Home</a></li>
    <li><a title="About Us" href="/pages/about-us">About Us</a></li>
</ul>
```

`left-menu` を作るには、ビューで次のコードを使います。

```php
echo $this->MenuBuilder->build('left-menu');
```

Item 4 (`/items/view/4`) ページでは、次のように出力されます。

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

最上位のラッパーには、メニュー名が id と class として付きます。`build()` の `class` オプションで
CSS クラスを追加できます。

```php
// <ul class="main-menu fun green" id="main-menu">
echo $this->MenuBuilder->build('main-menu', ['class' => 'fun green']);
```

`build()` のそれ以外のオプションは [設定](#settings) と同じもので、指定するとそれ以降はヘルパーの設定を上書きします。

<a id="settings"></a>

## 設定

ヘルパーに設定を渡せます。

```php
public $helpers = [
    'MenuBuilder.MenuBuilder' => [/* array of settings */],
];
```

<a id="default-settings"></a>

### デフォルトの設定

設定を何も指定しない場合は、次のデフォルトが使われます。

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

<a id="settings-details"></a>

### 設定の詳細

**activeClass**
選択中・現在の項目とその祖先に付ける CSS クラス名です。*(デフォルト `'active'`)*

**firstClass**
各階層の最初の項目に付ける CSS クラス名です。*(デフォルト `'first-item'`)*

**childrenClass**
サブメニューを含む項目に付ける CSS クラス名です。*(デフォルト `'has-children'`)*

**menuClass**
最上位のラッパー (`ul`) に付ける CSS クラス名です。*(デフォルト `null`)*

**wrapperClass**
サブメニューのラッパーに付ける CSS クラス名です。*(デフォルト `null`)*

**evenOdd**
`true` にすると、各項目に偶数・奇数のクラス名が付きます。*(デフォルト `false`)*

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
メニュー項目のフォーマットです。`li` 以外のタグを使いたい場合に指定します。*(デフォルト
`'<li%s>%s%s</li>'`)*

**wrapperFormat**
メニュー項目のコンテナのフォーマットです。`ul` 以外のタグを使いたい場合に指定します。*(デフォルト
`'<ul%s>%s</ul>'`)*

**noLinkFormat**
URL のない項目のフォーマットです。*(デフォルト `'<a href="#">%s</a>'`)*

*設定の例*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<div%s>%s%s</div>',
    'wrapperFormat' => '<div%s>%s</div>',
    'noLinkFormat' => '<div>%s</div>',
],
```

*出力の例 (`noLinkFormat` を説明するために URL のない項目を追加しています)*

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
すべてのメニューを含むビュー変数の名前です。*(デフォルト `'menu'`)*

**indentHtmlOutput**
生成する HTML をインデントします。*(デフォルト `true`)*

**defaults**
各項目の [オプション](#menu-item-options) のデフォルトで、すべての項目にマージされます。

次の設定は、権限に応じたメニュー (後述) に使われます。

**authVar**
ユーザーのデータを含むビュー変数の名前です。*(デフォルト `'user'`)*

**authModel**
認証モデルの名前です。*(デフォルト `'User'`)*

**authField**
ユーザーの種別・グループ・権限・レベルを含むフィールドの名前です。*(デフォルト `'group'`)*

<a id="permission-based-menu"></a>

## 権限に応じたメニュー

次のような `users` テーブルがあるとします。

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

ヘルパーは、グループを `$user['User']['group']` から読み取ります (`authVar`、`authModel`、
`authField` をご覧ください)。認証に Auth コンポーネントを使っている場合は、`AppController` に
次のように追加します。

```php
public function beforeFilter()
{
    parent::beforeFilter();

    $user = ['User' => $this->Auth->user()];
    $this->set(compact('user'));
}
```

認証に [Authsome](https://github.com/felixge/cakephp-authsome) を使っている場合は、代わりに次のように
追加します。

```php
public function beforeFilter()
{
    parent::beforeFilter();

    $user = Authsome::get();
    $this->set(compact('user'));
}
```

次に、メニューで権限を定義します。

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

<a id="menu-item-options"></a>

## メニュー項目のオプション

**title**
項目のタイトルです。タイトルのない項目 (`separator` もないもの) は出力されません。

**url**
項目の URL で、文字列または URL 配列で指定します。URL のない項目には `noLinkFormat` が使われます。

**children**
サブメニュー項目の配列です。

**permissions**
項目を表示できる種別・グループ・権限・レベルの配列です。*(デフォルト `[]`、全員)*

**partialMatch**
通常、URL の照合は厳密です。`/items/details` にいて、メニューに `/items` の項目がある場合、
デフォルトではその項目はアクティブになりません。`partialMatch` を `true` にすると、アクティブになります。
*(デフォルト `false`)*

**id**
項目の CSS の id です。*(デフォルト `null`)*

**class**
項目の CSS クラスで、文字列または配列で指定します。*(デフォルト `null`)*

**linkClass**
リンクの CSS クラスです。*(デフォルト `null`)*

**target**
リンクの `target` 属性です。*(デフォルト `null`)*

**escape**、**escapeTitle**
`HtmlHelper::link()` に渡されます。

**image**
リンクの中に表示する画像です。その後に、`label` クラスの `span` に入ったタイトルが続きます。
*(デフォルト `null`)*

**separator**
項目の代わりに、指定した HTML を出力します。以下は、これを使ってできることの例です。
*(デフォルト `false`)*

*設定の例*

```php
'MenuBuilder.MenuBuilder' => [
    'itemFormat' => '<dd%s>%s%s</dd>',
    'wrapperFormat' => '<dl%s>%s</dl>',
    'noLinkFormat' => '<dd>%s</dd>',
],
```

*メニューの例*

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

*出力の例*

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

<a id="origin"></a>

## 由来

このパッケージは
[torifat/cake-menu_builder](https://github.com/torifat/cake-menu_builder)
(Packagist では `torifat/cake-menu_builder`、アップストリームの最終バージョンは v2.0.3)
のメンテナンスされたフォークで、元は Rifat Nabi によって作られました。
[pieceofcake2](https://github.com/pieceofcake2) が、モダンな PHP 上の CakePHP 2 向けにメンテナンスしています。
オリジナルからの変更点は
[フォーク以降のコミット履歴](https://github.com/pieceofcake2/menu_builder/compare/b515602...2.x)
にあります。
