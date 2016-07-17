# Introduction #

This is a small wrapper to Composer class loader intended to add functionality similar to static initializers in Java.
You can define static method named `__constructStatic()` and it'll be invoked first time class loaded into project.

Example:

```php
class MyTestClass
{
    private static function __constructStatic()
    {
        //this will be called once after class loaded
    }
}
```

# Reqirements #

No external libraries required, just use Composer autoloader in your project.

# Installation #

Add to `composer.json` `require` block: `"vladimmi/construct-static": "dev-master@dev"`

# Usage #

```php
$composer = require_once(__DIR__ . '/vendor/autoload.php');     //get Composer loader
$loader = new ConstructStatic\Loader($composer);                //wrap it   
```

# Details #

Composer autoloader is unregistered and wrapped with this one. Then all class load calls go to Composer through
this loader. You can use `$loader` from the sample above as original `$composer` object - all methods calls
are proxied to wrapped loader.

Other possible autoloaders remain registered so check resulting loaders order to prevent unexpected results.

# Options #

## Process previously loaded classes ##

If you want to call static constructors on classes that were loaded before wrapper created, you can use
`processLoadedClasses` method to do this:

```php
$composer = require_once(__DIR__ . '/vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);
$loader->processLoadedClasses();        //call constructors on every already loaded class
```

## Pass custom data to called constructors

You can pass some data to called constructors - for example, inject services or pass DI container. To do this you need
to modify constructor a bit:

```php
class MyTestClass
{
    //Added $params parameter
    private static function __constructStatic($params = [])
    {
        //this will be called once after class loaded
    }
}
```

Then you can set needed data when creating wrapping loader:

```php
$composer = require_once(__DIR__ . '/vendor/autoload.php');
$params = [
    //set any needed data here...
];
$loader = new ConstructStatic\Loader($composer, $params);   //...and pass it to loader
```

That `$params` will be passed to every called constructor. If you want to pass some set of parameters to only specified
classes to prevent conflicts or any other reason, you can set them this way:

```php
$composer = require_once(__DIR__ . '/vendor/autoload.php');
$params = [
    //set any default data here
];
$anyYourClassParams = [
    //set any data for specific class here
];
$loader = new ConstructStatic\Loader($composer, $params);               //pass default data to loader
$loader->setClassParameters(AnyYourClass::class, $anyYourClassParams);  //pass data for specific class to loader
```

Then when `AnyYourClass` will be loaded it will receive `$anyYourClassParams` instead of `$params` while any other
class will receive `$params` which are set as default.