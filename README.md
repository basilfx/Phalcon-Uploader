# Phalcon-Uploader
File upload handler for the Phalcon Framework.

## Introduction
This package provides a file upload service.

You can use it as follows:

```php
// Assuming uploader is registered in dependency injector.
$this->uploader->add("file", [
    "name" => "path/and/name/of/file.ext",
    "required" => false,
]);

$result = $this->uploader->save();

if ($result) {
    print_r($result);
}
```

## Requirements
* PHP 7.0 or later.
* Phalcon Framework 3.0 or later.

## Installation
Install this dependency using `composer require basilfx/phalcon-uploader`.

## License
See the `LICENSE` file (MIT license).
