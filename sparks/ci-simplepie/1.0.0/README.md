# CodeIgniter SimplePie Integration

CodeIgniter SimplePie Integration is a simple library to use SimplePie within your CodeIgniter application.
The latest SimplePie version (1.3.0) is included in this release

## Requirements
1. CodeIgniter 2.0.0+

## Installing

Available via CodeIgniter Sparks. For info about how to install sparks, go here: http://getsparks.org/install

You can then load the spark with this:

```php
$this->load->spark('ci-simplepie/1.0.0/');
```

or by autoloading:

```php
$autoload['sparks'] = array('ci-simplepie/1.0.0');
```

## Usage

After loading, you have this object available:

```php
$this->cisimplepie;
```
This is just the regular SimplePie object, so for further assistance on how to use SimplePie, you can have a look at the SimplePie documentation over here: http://simplepie.org/wiki/ 