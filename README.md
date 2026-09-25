# Dir

[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/dir.svg)](https://packagist.org/packages/h4kuna/dir)
[![Latest Stable Version](https://poser.pugx.org/h4kuna/dir/v/stable?format=flat)](https://packagist.org/packages/h4kuna/dir)
[![Coverage Status](https://coveralls.io/repos/github/h4kuna/dir/badge.svg?branch=main)](https://coveralls.io/github/h4kuna/dir?branch=main)
[![Total Downloads](https://poser.pugx.org/h4kuna/dir/downloads?format=flat)](https://packagist.org/packages/h4kuna/dir)
[![License](https://poser.pugx.org/h4kuna/dir/license?format=flat)](https://packagist.org/packages/h4kuna/dir)

Part of the [h4kuna PHP libraries](https://github.com/h4kuna/library), see the overview of all packages.

Every directory of your project is represented by its own class. The class knows its absolute path and creates subdirectories on demand.

### Install by composer
```sh
composer require h4kuna/dir
```

Requires PHP 8.0.

## How to use

Your directory is represented by your class, extend the class `h4kuna\Dir\Dir`. The library already contains the class `TempDir`, create your own classes the same way.

Let's say the project has these directories:
- temp dir `/document/root/temp`
- log dir `/document/root/log`
- storage dir `/document/root/data`

### Example
Create StorageDir.

```php
class StorageDir extends \h4kuna\Dir\Dir
{

}
```

Start to use it.
```php
$storageDir = new StorageDir('/document/root/data'); // the constructor does not check the directory
$storageDir->create(); // create the directory from the constructor if it does not exist
$subDir = $storageDir->dir('foo/bar'); // creates the subdirectory
$filepath = $subDir->filename('lucky', 'jpg');
$filepath2 = $storageDir->filename('baz/foo/happy.jpg'); // creates the directory baz/foo

echo $filepath; // /document/root/data/foo/bar/lucky.jpg
echo $filepath2; // /document/root/data/baz/foo/happy.jpg
echo $storageDir; // /document/root/data, same as $storageDir->getDir()
```
The paths `/document/root/data/foo/bar` and `/document/root/data/baz/foo` now exist on the filesystem. The files themselves are not created.

Your storage dir is represented by the class StorageDir and you can use it with dependency injection.

```php
class MyClass {

    public function __construct(private StorageDir $storageDir) {
    }

}
```

## TempDir

`TempDir` accepts an absolute path, which is used as is. A relative path or no path is placed in the system temp directory under `h4kuna` and created immediately.

```php
use h4kuna\Dir\TempDir;

new TempDir('/document/root/temp'); // /document/root/temp
new TempDir('cache'); // /tmp/h4kuna/cache
```

## Check dir

If the directory cannot be created, the method `create()` throws `IOException`. If the directory exists but is not writable, the method `checkWriteable()` throws `DirIsNotWriteableException`, and `checkReadable()` throws `DirIsNotReadableException` if it is not readable. Both extend `IOException`.

```php
use h4kuna\Dir;

try {
    $fileInfo = (new Dir\Dir('/any/path'))
        ->create()
        ->checkWriteable()
        ->fileInfo('foo.txt');

    var_dump($fileInfo->getPathname()); // /any/path/foo.txt
} catch (Dir\Exceptions\IOException $e) {
    // dir cannot be created or is not writable
}
```

## Filesystem

The second parameter of the constructor is an implementation of `h4kuna\Dir\Storage\Filesystem`. The default `Local` works with the real filesystem, the directory mode is set in its constructor (`new Local(0755)`, default `0777`). `DevNull` does nothing and all checks pass, it is useful in tests.

```php
use h4kuna\Dir\Storage\DevNull;

$dir = new StorageDir('/any/path', new DevNull());
$dir->dir('foo')->filename('bar', 'json'); // /any/path/foo/bar.json, nothing is created
```

## Incorrect use

In the constructor use only an absolute path without a trailing slash, like in the example.

This is incorrect

- `new StorageDir('/document/root/data/')`
- `new StorageDir('document/root/data/')`
- `new StorageDir('document/root/data')`

Correct is only `new StorageDir('/document/root/data')`.

In the methods `dir()` and `filename()` don't use slashes at the beginning and at the end of the path.

This is incorrect

- `$storageDir->dir('/foo/')`
- `$storageDir->dir('/foo')`
- `$storageDir->dir('foo/')`

Correct is only `$storageDir->dir('foo')` or a subdirectory `$storageDir->dir('foo/bar')`.
