PhpMinifi
=========

Class for minifing PHP files

* it allows minifi more files together
* it allows retain PHPDocs

```php
// default do not include PHPDocs
$minifi = new \PhpMinifi\Minifi();

// set header of minifi version
$minifi->setHeader('PhpMinifi: Minified version of PhpMinifi. @author Viktor Mašíček');

// default include PHPDocs
$minifi->setDefaultPhpdocs(\PhpMinifi\Minifi::PHPDOCS_INCLUDE);

// one file with PHPDocs
$minifi->add('./myFile_A.php');
// one directory with PHPDocs
$minifi->add('./myDirectory_A');
// one files and one directory with PHPDocs
$minifi->add(array('./myFile_B.php', './myDirectory_B'));
// more files and directories without PHPDocs
$minifi->add(
    array(
        './myFile_C.php',
        './myFile_E.php'
        './myDirectory_C',
        './myDirectory_E',
    ),
    \PhpMinifi\Minifi::PHPDOCS_REMOVE
);

// get minifi content into variable
$content = $minifi->minifi();

// save minify content into set file and return it
$content = $minifi->minifi('./myMinifi.php');
```
