<?php declare(strict_types=1);

namespace h4kuna\Dir;

use Nette\Utils\FileSystem;

/**
 * You don't fill last slash in path
 *
 * @phpstan-consistent-constructor
 */
abstract class Dir
{
	public function __construct(private string $baseAbsolutePath)
	{
	}


	public function getDir(): string
	{
		return $this->baseAbsolutePath;
	}


	/**
	 * Make absolute path with filename
	 * @param string $name doesn't start with slash
	 */
	public function filename(string $name, string $extension = ''): string
	{
		$path = dirname($name);
		if ($path !== '.') {
			return $this->dir($path)->filename(basename($name), $extension);
		} elseif ($extension !== '') {
			$name .= ".$extension";
		}

		return self::slash($this->baseAbsolutePath, $name);
	}


	/**
	 * Add relative path from $baseAbsolutePath
	 * @example both is possible 'foo' or 'foo/bar'
	 */
	public function dir(string $path): static
	{
		$newDir = self::slash($this->baseAbsolutePath, $path);
		FileSystem::createDir($newDir);

		return new static($newDir);
	}


	public function __toString()
	{
		return $this->getDir();
	}


	final protected static function makeHomeDir(string $path, string $home = ''): string
	{
		if (FileSystem::isAbsolute($path) === false) {
			if ($home === '') {
				$home = self::slash(sys_get_temp_dir(), 'h4kuna');
			}
			$path = self::slash($home, $path);
			FileSystem::createDir($path); // intentionally here in condition branch
		}

		return $path;
	}


	final protected static function slash(string $dir1, string $dir2): string
	{
		return "$dir1/$dir2";
	}

}
