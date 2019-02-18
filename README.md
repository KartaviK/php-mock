# PHP-Mock: mocking built-in PHP functions

[![Build Status](https://travis-ci.com/KartaviK/php-mock.svg?branch=master)](https://travis-ci.com/KartaviK/php-mock)
[![codecov](https://codecov.io/gh/KartaviK/php-mock/branch/master/graph/badge.svg)](https://codecov.io/gh/KartaviK/php-mock)

PHP-Mock is a testing library which mocks non deterministic built-in PHP functions like
`time()` or `rand()`. This is achieved by [PHP's namespace fallback policy](http://php.net/manual/en/language.namespaces.fallback.php):

> PHP will fall back to global functions […]
> if a namespaced function […] does not exist.

PHP-Mock uses that feature by providing the namespaced function. I.e. you have
to be in a **non global namespace** context and call the function
**unqualified**:

```php
namespace foo;

$time = time(); // This call can be mocked, a call to \time() can't.
```

## Requirements and restrictions

* Only *unqualified* function calls in a namespace context can be mocked.
  E.g. a call for `time()` in the namespace `foo` is mockable,
  a call for `\time()` is not.

* The mock has to be defined before the first call to the unqualified function
  in the tested class. This is documented in [Bug #68541](https://bugs.php.net/bug.php?id=68541).
  In most cases, you can ignore this restriction but if you happen to run into
  this issue you can call `Mock::define()`
  before that first call. This would define a side effectless namespaced
  function which can be enabled later. Another effective
  approach is running your test in an isolated process.

## Alternatives

If you can't rely on or just don't want to use the namespace fallback policy,
there are alternative techniques to mock built-in PHP functions:

* [**PHPBuiltinMock**](https://github.com/jadell/PHPBuiltinMock) relies on
  the [APD](http://php.net/manual/en/book.apd.php) extension.

* [**MockFunction**](https://github.com/tcz/phpunit-mockfunction) is a PHPUnit
  extension. It uses the [runkit](http://php.net/manual/en/book.runkit.php) extension.

* [**UOPZ**](https://github.com/krakjoe/uopz) is a Zend extension which
  allows, among others, renaming and deletion of functions.

* [**vfsStream**](https://github.com/mikey179/vfsStream) is a stream wrapper for
  a virtual file system. This will help you write tests which covers PHP
  stream functions (e.g. `fread()` or `readdir()`).

# Installation

Use [Composer](https://getcomposer.org/):

```sh
composer require --dev kartavik/php-mock
```

# Usage

You don't need to learn yet another API. PHP-Mock has integrations
for these testing frameworks:

- [kartavik/php-mock-phpunit](https://github.com/php-mock/php-mock-phpunit) - PHPUnit integration

**Note:** If you plan to use one of the above mentioned testing frameworks you can skip
reading any further and just go to the particular integration project.

## PHP-Mock API

Create a `Mock`
object. You can do this with the fluent API of `MockBuilder`:

* `MockBuilder::setNamespace()`
  sets the target namespace of the mocked function.

* `MockBuilder::setName()`
  sets the name of the mocked function (e.g. `time()`).

* `MockBuilder::setFunction()`
  sets the concrete mock implementation.

* `MockBuilder::setFunctionProvider()`
  sets, alternativly to `MockBuilder::setFunction()`, the mock implementation as a
  `FunctionProvider`:

   * `FixedValueFunction`
     is a simple implementation which returns always the same value.

   *`FixedMicrotime`
     is a simple implementation which returns always the same microtime. This
     class is different to `FixedValueFunction` as it contains a converter for
     `microtime()`'s float and string format.

   * `FixedDate`
     is a simple implementation which returns always a formated date for the fixed timestamp.

   * `SleepFunction`
     is a `sleep()` implementation, which doesn't halt but increases an
     `Increment`
     e.g. a `time()` mock.

   * `UsleepFunction`
     is an `usleep()` implementation, which doesn't halt but increases an
     `Increment` e.g. a `microtime()` mock.

* `MockBuilder::build()`
  builds a `Mock` object.

After you have build your `Mock` object you have to call `enable()`
to enable the mock in the given namespace. When you are finished with that mock you
should disable it by calling `disable()`
on the mock instance. 

This example illustrates mocking of the unqualified function `time()` in the 
namespace `foo`:

```php
namespace foo;

use phpmock\MockBuilder;

$builder = new MockBuilder();
$builder->setNamespace(__NAMESPACE__)
    ->setName("time")
    ->setFunction(
        function () {
            return 1417011228;
        }
    );
                    
$mock = $builder->build();

// The mock is not enabled yet.
assert (time() != 1417011228);

$mock->enable();
assert (time() == 1417011228);

// The mock is disabled and PHP's built-in time() is called.
$mock->disable();
assert (time() != 1417011228);
```

Instead of setting the mock function with `MockBuilder::setFunction()` you could also
use the existing `FixedValue`:

```php
namespace foo;

use Kartavik\PHPMock\MockBuilder;
use Kartavik\PHPMock\Functions\FixedValue;

$builder = new MockBuilder();
$builder->setNamespace(__NAMESPACE__)
    ->setName("time")
    ->setFunctionProvider(new FixedValueFunction(1417011228));

$mock = $builder->build();
```

### Reset global state

An enabled mock changes global state. This will break subsequent tests if
they run code which would call the mock unintentionally. Therefore
you should always disable a mock after the test case. You will have to disable
the created mock. You could do this for all mocks by calling the
static method
`Mock::disableAll()`.

### Mock environments

Complex mock environments of several mocked functions can be grouped in a `Environment\Mock`:

* `Environment\Mock::enable()`
  enables all mocked functions of this environment.

* `Environment\Mock::disable()`
  disables all mocked functions of this environment.

* `Environment\Mock::define()`
  defines all mocked functions of this environment.

#### SleepEnvironmentBuilder

The `Environment\SleepBuilder`
builds a mock environment where `sleep()` and `usleep()` return immediatly.
Furthermore they increase the amount of time in the mocked `date()`, `time()` and
`microtime()`:

```php
namespace foo;

use Kartavik\Environment\SleepBuilder;

$builder = new SleepEnvironmentBuilder();
$builder->addNamespace(__NAMESPACE__)
    ->setTimestamp(1417011228);

$environment = $builder->build();
$environment->enable();

// This won't delay the test for 10 seconds, but increase time().        
sleep(10);

assert(1417011228 + 10 == time());
```

If the mocked functions should be in different namespaces you can
add more namespaces with [`Environment\SleepBuilder::addNamespace()`](http://php-mock.github.io/php-mock/api/class-phpmock.environment.SleepEnvironmentBuilder.html#_addNamespace)

# Authors and contributors

Fork contributor - [Roman Vakura](mailto:roman.varkuta@gmail.com)

Author - this project is fork of [php-mock/php-mock](https://github.com/php-mock/php-mock)

# License
- [WTFPL](./LICENSE)
