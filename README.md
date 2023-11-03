# Extended version of var_dump

Sometimes you need to know what's inside a variable, but you don't want to use var_dump() because it's too verbose or whatever. This function is a better alternative. Just pass 
it a variable and it will return a page with the variable's contents in a readable format.

## Usage

```php
<?php

// get autoloader
require_once 'vendor/autoload.php';

// use the function
varDebug($variable);
```

## Installation

```bash
composer require --dev basteyy/vardump
```
