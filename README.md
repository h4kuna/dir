# Dir

[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/dir.svg)](https://packagist.org/packages/h4kuna/dir)
[![Latest Stable Version](https://poser.pugx.org/h4kuna/dir/v/stable?format=flat)](https://packagist.org/packages/h4kuna/dir)
[![Coverage Status](https://coveralls.io/repos/github/h4kuna/dir/badge.svg?branch=master)](https://coveralls.io/github/h4kuna/dir?branch=master)
[![Total Downloads](https://poser.pugx.org/h4kuna/dir/downloads?format=flat)](https://packagist.org/packages/h4kuna/dir)
[![License](https://poser.pugx.org/h4kuna/dir/license?format=flat)](https://packagist.org/packages/h4kuna/dir)

One abstract class provide path and prepare filesystem.

### Install by composer 
```
composer require h4kuna/dir
```

## How to use

Your path is represented by your class. In this repository is prepared class TempDir. By same way create own class.


These dirs are exist:
- temp dir `/documet/root/temp`
- log dir `/documet/root/log`
- storage dir `/documet/root/data`

### Example
Create StorageDir.

```php
class StorageDir extends \h4kuna\Dir\Dir 
{

}
```

Start to use.
```php
$storageDir = new StorageDir('/documet/root/data'); // dir in constructor does not check
$storageDir->create(); // if dir from constructor does not exist, let's check and create
$subDir = $storageDir->dir('foo/bar');
$filepath = $subDir->filename('lucky', 'jpg');
$filepath2 = $storageDir->filename('baz/foo/happy.jpg');

echo $filepath; // /documet/root/data/foo/bar/lucky.jpg
echo $filepath2; // /documet/root/data/baz/foo/happy.jpg
```
On file system exists path `/documet/root/data/foo/bar` and `/documet/root/data/baz/foo`.

Your storage dir is represented by class StorageDir and you can use it by dependency injection.

```php
class MyClass {

    public function __construct(private StorageDir $storageDir) {
    }
    
}
```

## Check dir

If directory does not exist, the method `create` throw IOException. If directory exists, but is not writeable, the method `checkWriteable` throw `DirIsNotWriteableException` extends from `IOException`.

```php
use h4kuna\Dir;
try {
    $fileInfo = (new Dir\Dir('/any/path'))
        ->create()
        ->checkWriteable()
        ->fileInfo('foo.txt');
} catch (Dir\Exceptions\IOException $e) {
    // dir is not writable
}

var_dump($fileInfo->getPathname()); // /any/path/foo.txt
```

## Incorrect use

In constructor use only absolute path without last slash like in example. 

This is incorrect

- `new StorageDir('/documet/root/data/')`
- `new StorageDir('documet/root/data/')`
- `new StorageDir('documet/root/data')`

Correct is only `new StorageDir('/documet/root/data')`.

In methods `dir()` and `filename()` don't use slashes on the begin and end path.

This is incorrect

- `$storageDir->dir('/foo/')`
- `$storageDir->dir('/foo')`
- `$storageDir->dir('foo/')`
  
Correct is only `$storageDir->dir('foo')` or sub dir `$storageDir->dir('foo/bar')`.
