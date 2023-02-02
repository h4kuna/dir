# Dir

[![Latest stable](https://img.shields.io/packagist/v/h4kuna/dir.svg)](https://packagist.org/packages/h4kuna/dir)
[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/dir.svg)](https://packagist.org/packages/h4kuna/dir)

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

Start to use. Path in constructor must exist!
```php
$storageDir = new StorageDir('/documet/root/data');
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
