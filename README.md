# Funcdown - Advanced Markup Generator
In contrast to [SR (Standard-Rolling) **Funcdown** version from official website](https://hngts.com/?mkp=fncd~obtain) that consists of class, library and configuration file separatedly, this version has all three of them packed into one fat php file. Thanks to PHP's 'multispace' feature (`namespace NsName {}`) we don't need to mess around with .phar extension any longer. 

## Structure Difference against Funcdown SR version
- Configuration sits inside `RUNTIME_CONFIGURATION` class constant
- Toggler config values are preffered as literal booleans
- Trait `ini_config____` is completely removed
  ### Additional Auto-Instancing Option
  - This variant of **Funcdown** has additional `boolean` class constant called `NAMED_FUNCDOWN_GLOBAL`
  - The purpose of this constant is to trigger automatic instance declaration behind named constant `'Funcdown'`
  - Its default value is set to `false` and in that state will not trigger anything.
  - Class constant `NAMED_FUNCDOWN_GLOBAL` can be found right before `RUNTIME_CONFIGURATION` class constant.

`RUNTIME_CONFIGURATION` class constant can be found at the bottom of the `funcdown.php` file.

## Example usage
Save the contents into desired _`filename.php`_.

`funcdown-standalone.php` if nothing better comes to mind, and:
 
```php
  use \H\scope\Funcdown; // Alias
  require_once 'funcdown-standalone.php';
```
After this point, if autoinstance is turned on, just use it
```php
  echo Funcdown-> parse ('article (. cName) [ h1 {Headline Title}
    ol [ li {} li {} li {} li {} li {} li {} li {} li {} ]
  ]')-> release();
```
Without it ? .. nothing new under the sun.
```php
  $Funcdown = new Funcdown;
```
Be it Funcdown behind variable or behind Named Constant, there's always [official manual](https://hngts.com/?mkp=fncd~manual) for further help.
