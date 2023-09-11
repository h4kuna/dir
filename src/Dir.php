<?php declare(strict_types=1);

namespace h4kuna\Dir;

use Nette;
use Nette\Utils\FileSystem;
use SplFileInfo;
use Stringable;

/**
 * You don't fill last slash in path
 *
 * @phpstan-consistent-constructor
 */
class Dir implements Stringable
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
	 * @throws Exceptions\IOException
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
	 * @throws Exceptions\IOException
	 */
	public function fileInfo(string $name, string $extension = ''): SplFileInfo
	{
		return new SplFileInfo($this->filename($name, $extension));
	}


	/**
	 * Add relative path from $baseAbsolutePath
	 * @throws Exceptions\IOException
	 * @example both is possible 'foo' or 'foo/bar'
	 */
	public function dir(string $path): static
	{
		$newDir = self::slash($this->baseAbsolutePath, $path);;

		return new static(self::createDir($newDir));
	}


	/**
	 * @throws Exceptions\IOException
	 */
	public function create(): static
	{
		self::createDir($this->baseAbsolutePath);

		return $this;
	}


	/**
	 * @throws Exceptions\DirIsNotWriteableException
	 */
	public function checkWriteable(): static
	{
		if (is_writable($this->baseAbsolutePath) === false) {
			throw new Exceptions\DirIsNotWriteableException($this->baseAbsolutePath);
		}

		return $this;
	}


	/**
	 * @throws Exceptions\DirIsNotReadableException
	 */
	public function checkReadable(): static
	{
		if (is_readable($this->baseAbsolutePath) === false) {
			throw new Exceptions\DirIsNotReadableException($this->baseAbsolutePath);
		}

		return $this;
	}


	public function __toString()
	{
		return $this->getDir();
	}


	/**
	 * @throws Exceptions\IOException
	 */
	final protected static function makeHomeDir(string $path, string $home = ''): string
	{
		if (FileSystem::isAbsolute($path) === false) {
			if ($home === '') {
				$home = self::slash(sys_get_temp_dir(), 'h4kuna');
			}
			$path = self::createDir(self::slash($home, $path)); // intentionally here in condition branch
		}

		return $path;
	}


	final protected static function slash(string $dir1, string $dir2): string
	{
		return "$dir1/$dir2";
	}


	/**
	 * @throws Exceptions\IOException
	 */
	private static function createDir(string $path): string
	{
		try {
			FileSystem::createDir($path);
		} catch (Nette\IOException $e) {
			throw new Exceptions\IOException($path, 0, $e);
		}

		return $path;
	}

}
