# Hyperf 枚举组件

## 关于

简单、可扩展且强大的枚举实现。

* 枚举键值对即类常量
* 功能完备的方法集
* 枚举实例化
* 位标志/位移枚举
* 类型提示
* 属性转换
* 枚举命令生成器
* 用于将枚举键或值作为输入参数传递的验证规则
* 本地化支持
* 可通过宏扩展

> 移植自 [`bensampo/laravel-enum`](https://github.com/BenSampo/laravel-enum) 。

## 索引

* [安装](#安装)
* [基本使用](#基本使用)
    * [枚举定义](#枚举定义)
    * [实例化](#实例化)
    * [实例属性](#实例属性)
    * [实例类型转换](#实例类型转换)
    * [实例相等性比较](#实例相等性比较)
    * [类型提示](#类型提示)
* [位标志/位移枚举](#位标志位移枚举)
* [属性转换](#属性转换)
* [数据库迁移](#数据库迁移)
* [验证器](#验证器)
* [本地化](#本地化)
* [重写 getDescription 方法](#重写-getdescription-方法)
* [扩展枚举基类](#扩展枚举基类)
* [PHPStan 集成](#phpstan-集成)
* [命令列表](#命令列表)
* [Enum 类参考](#enum-类参考)

## 安装

```shell script
composer require hyperf-ext/enum
```

## 基本用法

### 枚举定义

可以使用一些命令来生成新的枚举类：

```shell script
php bin/hyperf.php gen:enum UserType
```

现在只需将所有可能值作为常量添加到枚举类即可。

```php
<?php

declare(strict_types=1);

namespace App\Enum;

use HyperfExt\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;
}
```

这样就完成了。注意，由于枚举值被定义为常量，你可以像使用其他类常量一样简单地使用它们。

```php
UserType::Administrator // 值为 `0`
```

### 实例化

实例化枚举有助于在传递它们时带来类型提示的益处。

此外，由于无效的值不能被实例化，因此可以确保传递的值是始终有效的。

为了方便，枚举可以通过多种方式来实例化：

```php
// 标准的 PHP 实例化方式，将期望的枚举值作为参数进行传递
$enumInstance = new UserType(UserType::Administrator);

// 与使用构造函数一样，用值来实例化
$enumInstance = UserType::fromValue(UserType::Administrator);

// 使用枚举的键名作为参数来实例化
$enumInstance = UserType::fromKey('Administrator');

// 以键名作为方法名来静态调用，利用 __callStatic 魔术方法
$enumInstance = UserType::Administrator();

// 尝试使用给定的键名或值实例化一个枚举。如果无法实例化，则返回 `null`
$enumInstance = UserType::coerce($someValue);
```

如果要使用 IDE 的自动完成，则可以通过命令生成 PHPDoc 注释。

默认情况下，`app/Enum` 目录中的所有枚举都会被添加相关注释（可以通过将目录路径传递给 `--folder` 选项来更改目录）。

```shell script
php bin/hyperf.php enum:annotate
```

也可以通过指定类名来为某个类添加注释。

```shell script
php bin/hyperf.php enum:annotate "App\Enum\UserType"
```

### 实例属性

有了枚举实例后，可以将 `key`、`value` 和 `description` 作为属性来访问。

```php
$userType = UserType::fromValue(UserType::SuperAdministrator);

$userType->key; // SuperAdministrator
$userType->value; // 0
$userType->description; // Super Administrator
```

这个功能在要将枚举实例传递到视图的场景下会很有用。

### 实例类型转换

枚举实例可以在实现 `__toString()` 魔术方法时转换为字符串。这也意味着它们可以在视图中直接输出。

```php
$userType = UserType::fromValue(UserType::SuperAdministrator);

(string) $userType // '0'
```

### 实例相等性比较

我们可以通过将任意值传递给实例的 `is` 方法来确定实例是否与这些相等。为了方便起见，还有一个 `isNot` 方法，它与 `is` 方法的逻辑完全相反。

```php
$admin = UserType::fromValue(UserType::Administrator);

$admin->is(UserType::Administrator);   // true
$admin->is($admin);                    // true
$admin->is(UserType::Administrator()); // true

$admin->is(UserType::Moderator);       // false
$admin->is(UserType::Moderator());     // false
$admin->is('random-value');            // false
```

还可以使用 `in` 方法来确定实例的值是否与可能值的数组匹配。

```php
$admin = UserType::fromValue(UserType::Administrator);

$admin->in([UserType::Moderator, UserType::Administrator]);     // true
$admin->in([UserType::Moderator(), UserType::Administrator()]); // true

$admin->in([UserType::Moderator, UserType::Subscriber]);        // false
$admin->in(['random-value']);                                   // false
```

### 类型提示

另一个枚举实例的益处是让我们可以使用类型提示，如下所示。

```php
function canPerformAction(UserType $userType)
{
    if ($userType->is(UserType::SuperAdministrator)) {
        return true;
    }

    return false;
}

$userType1 = UserType::fromValue(UserType::SuperAdministrator);
$userType2 = UserType::fromValue(UserType::Moderator);

canPerformAction($userType1); // 返回 true
canPerformAction($userType2); // 返回 false
```

## 位标志/位移枚举

标准枚举一次只能表示一个值，但是位标志或位移枚举可以同时表示多个值。当要表达一组选项的多个选择时，这是最佳的选择。一个很好的例子就是用户权限，其中可能的权限数量有限，但是用户可以没有权限或是拥有全部或部分权限。

可以使用以下命令生成位标志枚举：

```shell script
php bin/hyperf.php gen:enum UserPermissions --flagged
```

### 定义值

定义值时，必须使用 `2` 的幂，最简单的方法是使用*左移位* `<<` 运算符，如下所示：

```php
final class UserPermissions extends FlaggedEnum
{
    const ReadComments      = 1 << 0;
    const WriteComments     = 1 << 1;
    const EditComments      = 1 << 2;
    const DeleteComments    = 1 << 3;
    // 接下来是 `1 << 4`，并依此类推…
}
```

### 定义位标志组合

可以使用按位*或* `|` 表示一组给定值的组合值作为快捷方式来使用。

```php
final class UserPermissions extends FlaggedEnum
{
    const ReadComments      = 1 << 0;
    const WriteComments     = 1 << 1;
    const EditComments      = 1 << 2;
    const DeleteComments    = 1 << 3;
    
    // 位标志组合
    const Member = self::ReadComments | self::WriteComments; // 读取和写入权限
    const Moderator = self::Member | self::EditComments; // Member 的所有权限，附加编辑权限
    const Admin = self::Moderator | self::DeleteComments; // Moderator 的所有权限，附加删除权限
}
```

### 实例化位标志枚举

有几种方法可以实例化位标志枚举：

```php
// 标准的 PHP 实例化方式，将所需的枚举值作为值数组或枚举实例数组传递
$permissions = new UserPermissions([UserPermissions::ReadComments, UserPermissions::EditComments]);
$permissions = new UserPermissions([UserPermissions::ReadComments(), UserPermissions::EditComments()]);

// 静态 flags 方法，同样将所需的枚举值作为值数组或枚举实例数组传递
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::EditComments]);
$permissions = UserPermissions::flags([UserPermissions::ReadComments(), UserPermissions::EditComments()]);
```

[属性转换](#属性转换)的工作方式与单值枚举相同。

### 空位标志枚举

位标志枚举可以不包含任何值。每个标记的枚举都有一个预定义的常量 `None`，相当于 `0`。

```php
UserPermissions::flags([])->value === UserPermissions::None; // True
```

### 位标志枚举方法

除了标准的枚举方法，位标志枚举还提供了一组有用的方法。

注意：在任何可以传递静态属性的地方，也可以传递枚举实例。

#### setFlags(array $flags): Enum

将枚举的位标志设置为给定的位标志数组。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->flags([UserPermissions::EditComments, UserPermissions::DeleteComments]); // 当前位标志为：EditComments, DeleteComments
```

#### addFlag($flag): Enum

添加给定的位标志到枚举。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->addFlag(UserPermissions::EditComments); // 当前位标志为：ReadComments, EditComments
```

#### addFlags(array $flags): Enum

添加一组给定的位标志到枚举。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->addFlags([UserPermissions::EditComments, UserPermissions::WriteComments]); // 当前位标志为：ReadComments, EditComments, WriteComments
```

#### removeFlag($flag): Enum

从枚举中移除给定的位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->removeFlag(UserPermissions::ReadComments); // 当前位标志为：WriteComments.
```

#### removeFlags(array $flags): Enum

从枚举中移除一组给定的位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments, UserPermissions::EditComments]);
$permissions->removeFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // 当前位标志为：EditComments
```

#### hasFlag($flag): bool

确定枚举中是否存在指定的位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasFlag(UserPermissions::ReadComments); // True
$permissions->hasFlag(UserPermissions::EditComments); // False
```

#### hasFlags(array $flags): bool

确定枚举中是否存在指定的全部位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // True
$permissions->hasFlags([UserPermissions::ReadComments, UserPermissions::EditComments]); // False
```

#### notHasFlag($flag): bool

确定枚举中是否不存在指定的位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->notHasFlag(UserPermissions::EditComments); // True
$permissions->notHasFlag(UserPermissions::ReadComments); // False
```

#### notHasFlags(array $flags): bool

确定枚举中是否不存在指定的任何一个位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->notHasFlags([UserPermissions::ReadComments, UserPermissions::EditComments]); // True
$permissions->notHasFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // False
```

#### getFlags(): Enum[]

将位标志作为实例数组返回。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->getFlags(); // [UserPermissions::ReadComments(), UserPermissions::WriteComments()];
```

#### hasMultipleFlags(): bool

确定枚举是否有多个位标志。

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasMultipleFlags(); // True;
$permissions->removeFlag(UserPermissions::ReadComments)->hasMultipleFlags(); // False
```

#### getBitmask(): int

获取枚举的位掩码。

```php
UserPermissions::Member()->getBitmask(); // 11;
UserPermissions::Moderator()->getBitmask(); // 111;
UserPermissions::Admin()->getBitmask(); // 1111;
UserPermissions::DeleteComments()->getBitmask(); // 1000;
```

## 属性转换

我们可以使用 Hyperf 内置的自定义类型转换将模型属性转换为枚举。这将在获取时将属性强制转换为枚举实例，并在设置时转回为枚举值。
由于 `Enum::class` 实现了 `Castable` 接口，因此只需要指定枚举的类名即可：

```php
use HyperfExt\Enum\Traits\CastsEnums;
use HyperfTest\Enum\Enums\UserType;
use Hyperf\Database\Model\Model;

class Example extends Model
{
    use CastsEnums;

    protected $casts = [
        'random_flag' => 'boolean',     // 标准的 hyperf 类型转换示例
        'user_type' => UserType::class, // 枚举类型转换示例
    ];
}
```

现在，当访问 `Example` 模型的 `user_type` 属性时，基础值将作为 `UserType` 枚举返回。

```php
$example = Example::first();
$example->user_type // UserType 实例
```

查阅[枚举实例可用的方法和属性](#实例化)，以充分了解属性转换。

我们还可以通过传递枚举值或另一个枚举实例来设置该值。

```php
$example = Example::first();

// 设置使用枚举值
$example->user_type = UserType::Moderator;

// 设置使用枚举实例
$example->user_type = UserType::Moderator();
```

### 转换基础原生类型

有些数据库会以字符串的形式返回所有内容（例如，整数可能以字符串 `'1'` 的形式返回）。

为了减少用户的麻烦，我们使用强类型来确定预期值。如果期望控制它，则可以在枚举类上重写 `parseDatabase` 静态方法：

```php
final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    
    public static function parseDatabase($value)
    {
        return (int) $value;
    }
}
```

从 `parseDatabase` 方法中返回 `null` 会使模型上的属性也为 `null`。如果数据库存储了不一致的空白值（例如空字符串）而不是 `NULL`，那么这会很有用。

## 数据库迁移

### 推荐

由于枚举在代码级别强制一致性，因此不必在数据库级别再做这些工作。依据枚举值，数据库字段的推荐类型为 `string` 或 `int`。这就意味着我们可以在代码中添加或删除枚举值，而不必顾及数据库层。

```php
use App\Enum\UserType;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('type')
                ->default(UserType::Moderator);
        });
    }
}
```

### 使用 `enum` 字段类型

另外，我们可以在迁移中使用枚举来定义枚举字段。枚举值必须定义为字符串。

```php
use App\Enum\UserType;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->enum('type', UserType::getValues())
                ->default(UserType::Member);
        });
    }
}
```

## 验证器

### 数组验证器

#### 枚举值

我们可以使用 `EnumValue` 规则来验证传递的枚举值是否是给定枚举的有效值。

```php
use HyperfExt\Enum\Rules\EnumValue;

/**
 * @Inject
 * @var \Hyperf\Validation\ValidatorFactory
 */
protected $validatorFactory;

public function store(Request $request)
{
    $validator = $this->validatorFactory->make($request->post(), [
        'user_type' => ['required', new EnumValue(UserType::class)],
    ]);
}
```

默认情况下，类型检查被设置为严格模式，但是可以通过向 `EnumValue` 类的可选第二个参数传递 `false` 来绕过此检查。

```php
new EnumValue(UserType::class, false) // 关闭严格模式类型检查
```

#### 枚举键

我们也可以使用 `EnumKey` 规则对枚举键进行验证。例如，如果将枚举键用作 URL 参数进行排序或过滤，这会很有用。

```php
use HyperfExt\Enum\Rules\EnumKey;

/**
 * @Inject
 * @var \Hyperf\Validation\ValidatorFactory
 */
protected $validatorFactory;

public function store(Request $request)
{
    $validator = $this->validatorFactory->make($request->post(), [
        'user_type' => ['required', new EnumKey(UserType::class)],
    ]);
}
```

#### 枚举实例

另外，我们可以验证参数是否为给定枚举的实例。

```php
use HyperfExt\Enum\Rules\Enum;

/**
 * @Inject
 * @var \Hyperf\Validation\ValidatorFactory
 */
protected $validatorFactory;

public function store(Request $request)
{
    $validator = $this->validatorFactory->make($request->post(), [
        'user_type' => ['required', new Enum(UserType::class)],
    ]);
}
```

### 管道验证

也可以将“管道”语法用于验证规则。

**enum_value**_:enum_class,[strict]_  
**enum_key**_:enum_class_  
**enum**_:enum_class_

```php
'user_type' => 'required|enum_value:' . UserType::class,
'user_type' => 'required|enum_key:' . UserType::class,
'user_type' => 'required|enum:' . UserType::class,
```

## 本地化

本地化支持依赖 `hyperf/translation` 组件，不要忘记发布其配置并按需设置。

```shell script
php bin/hyperf.php vendor:publish hyperf/translation
```

关于 `hyperf/translation` 组件的使用请阅读 [Hyperf 官方文档](https://hyperf.wiki/2.0/#/zh-cn/translation) 。

### 验证器错误信息

运行以下命令将语言文件发布到你的 `storage/languages` 目录中。

```shell script
php bin/hyperf.php vendor:publish hyperf-ext/enum
```

### 枚举描述

如果尚未通过 `vendor:publish` 命令发布语言文件，请先按照上面那条命令来发布。

在此示例中，我们配置了英语和西班牙语两个语言文件。

```php
// storage/languages/en/enum.php

use App\Enum\UserType;

return [
    // …

    UserType::class => [
        UserType::Administrator => 'Administrator',
        UserType::SuperAdministrator => 'Super administrator',
    ],

];
```

```php
// storage/languages/es/enum.php

use App\Enum\UserType;

return [
    // …

    UserType::class => [
        UserType::Administrator => 'Administrador',
        UserType::SuperAdministrator => 'Súper administrador',
    ],

];
```

最后，只需确保枚举类实现 `LocalizedEnum` 接口即可，如下所示：

```php
use HyperfExt\Enum\Enum;
use HyperfExt\Enum\Contracts\LocalizedEnum;

final class UserType extends Enum implements LocalizedEnum
{
    // ...
}
```

`getDescription` 方法将在语言文件中查找翻译的文本。如果给定键的值不存在，则返回默认描述。

## 重写 getDescription 方法

如果要通过 `getDescription` 方法返回自定义值，可以在枚举类中重写该方法来实现：

```php
public static function getDescription($value): string
{
    if ($value === self::SuperAdministrator) {
        return 'Super admin';
    }

    return parent::getDescription($value);
}
```

现在，调用 `UserType::getDescription(3);` 将返回 `Super administator` 而不是 `Super admin`。

## 扩展枚举基类

枚举基类 `Enum` 实现了 Hyperf Macroable 特性，这意味着我们可以使用自己的函数轻松对其进行扩展。如果有经常要添加到每个枚举的函数，则可以使用宏。

假设我们期望获得枚举类 `asArray` 方法的键值反转的版本，可以使用以下方法完成此操作：

```php
Enum::macro('asFlippedArray', function() {
    return array_flip(self::asArray());
});
```

现在在每个枚举中都可以使用 `UserType::asFlippedArray()` 来调用它。

建议通过监听服务启动相关事件来注册宏。

## PHPStan 集成

如果要使用 [PHPStan](https://github.com/phpstan/phpstan) 进行静态分析，可以启用扩展程序以正确识别魔术实例化方法。

将以下内容添加到项目的 `phpstan.neon` 的 includes 中：

```neon
includes:
- vendor/bensampo/laravel-enum/extension.neon
```

## 命令列表

```shell script
php bin/hyperf.php gen:enum
```
生成一个新的枚举类。传递 `--flagged` 选项可以创建位标志枚举。[了解更多](#枚举定义)

```shell script
php bin/hyperf.php enum:annotate
```
为枚举类生成 DocBlock 注释。[了解更多](#实例化)

## Enum 类参考

### static getKeys(): array

返回包含枚举所有键的数组。

```php
UserType::getKeys(); // 返回 ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator']
```

### static getValues(): array

返回包含枚举所有值的数组。

```php
UserType::getValues(); // 返回 [0, 1, 2, 3]
```

### static getKey(mixed $value): string

返回给定枚举值的键。

```php
UserType::getKey(1); // 返回 'Moderator'
UserType::getKey(UserType::Moderator); // 返回 'Moderator'
```

### static getValue(string $key): mixed

返回给定枚举键的值。

```php
UserType::getValue('Moderator'); // 返回 1
```

### static hasKey(string $key): bool

确定枚举中是否含有给定的键。

```php
UserType::hasKey('Moderator'); // 返回 'True'
```

### static hasValue(mixed $value, bool $strict = true): bool

确定枚举中是否含有给定的值。

```php
UserType::hasValue(1); // 返回 'True'

// 可以禁用严格类型检查：
UserType::hasValue('1'); // 返回 'False'
UserType::hasValue('1', false); // 返回 'True'
```

### static getDescription(mixed $value): string

以句首字母大写形式返回枚举键。可以通过[重写 getDescription 方法](#重写-getDescription-方法)来返回自定义描述。

```php
UserType::getDescription(3); // 返回 'Super administrator'
UserType::getDescription(UserType::SuperAdministrator); // 返回 'Super administrator'
```

### static getRandomKey(): string

随机返回枚举的一个键。

```php
UserType::getRandomKey(); // 返回 'Administrator', 'Moderator', 'Subscriber' 或 'SuperAdministrator'
```

### static getRandomValue(): mixed

随机返回枚举的一个值。

```php
UserType::getRandomValue(); // 返回 0, 1, 2 或 3
```

### static getRandomInstance(): mixed

随机返回枚举的一个实例。

``` php
UserType::getRandomInstance(); // 返回随机值的 UserType 实例
```

### static asArray(): array

将枚举键值作为关联数组返回。

```php
UserType::asArray(); // 返回 ['Administrator' => 0, 'Moderator' => 1, 'Subscriber' => 2, 'SuperAdministrator' => 3]
```

### static asSelectArray(): array

返回 value => description 形式的数组。

```php
UserType::asSelectArray(); // 返回 [0 => 'Administrator', 1 => 'Moderator', 2 => 'Subscriber', 3 => 'Super administrator']
```

### static fromValue(mixed $enumValue): Enum

返回被调用枚举的实例。进一步了解[枚举实例化](#实例化)。

``` php
UserType::fromValue(UserType::Administrator); // 返回 UserType 的实例，该实例的值为 UserType::Administrator
```

### static getInstances(): array

返回一个包含被调用枚举的所有可能实例的数组，以常量名作为键。

```php
var_dump(UserType::getInstances());

array(4) {
  'Administrator' =>
  class HyperfExt\Enum\Tests\Enums\UserType#415 (3) {
    public $key =>
    string(13) "Administrator"
    public $value =>
    int(0)
    public $description =>
    string(13) "Administrator"
  }
  'Moderator' =>
  class HyperfExt\Enum\Tests\Enums\UserType#396 (3) {
    public $key =>
    string(9) "Moderator"
    public $value =>
    int(1)
    public $description =>
    string(9) "Moderator"
  }
  'Subscriber' =>
  class HyperfExt\Enum\Tests\Enums\UserType#393 (3) {
    public $key =>
    string(10) "Subscriber"
    public $value =>
    int(2)
    public $description =>
    string(10) "Subscriber"
  }
  'SuperAdministrator' =>
  class HyperfExt\Enum\Tests\Enums\UserType#102 (3) {
    public $key =>
    string(18) "SuperAdministrator"
    public $value =>
    int(3)
    public $description =>
    string(19) "Super administrator"
  }
}
```

### static coerce(mixed $enumKeyOrValue): ?Enum

尝试使用给定的键或值实例化一个新的枚举。如果无法实例化，则返回 `null`。

```php
UserType::coerce(0); // 返回 UserType 的实例，该实例的值为 UserType::Administrator
UserType::coerce('Administrator'); // 返回 UserType 的实例，该实例的值为 UserType::Administrator
UserType::coerce(99); // 返回 null (无效的枚举值)
```
