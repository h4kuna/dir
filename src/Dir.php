<?php declare(strict_types=1);

namespace h4kuna\Dir;

use h4kuna\Dir\Storage\Filesystem;
use h4kuna\Dir\Storage\Local;
use SplFileInfo;
use Stringable;
use Throwable;

/**
 * You don't fill last slash in path
 *
 * @phpstan-consistent-constructor
 */
class Dir implements Stringable
{
	private Filesystem $filesystem;


	public function __construct(private string $baseAbsolutePath, ?Filesystem $filesystem = null)
	{
		$this->filesystem = $filesystem ?? new Local();
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
		$newDir = self::slash($this->baseAbsolutePath, $path);

		return new static(self::createDir($newDir, $this->filesystem), $this->filesystem);
	}


	/**
	 * @throws Exceptions\IOException
	 */
	public function create(): static
	{
		self::createDir($this->baseAbsolutePath, $this->filesystem);

		return $this;
	}


	/**
	 * @throws Exceptions\DirIsNotWriteableException
	 */
	public function checkWriteable(): static
	{
		if ($this->filesystem->isWriteable($this->baseAbsolutePath) === false) {
			throw new Exceptions\DirIsNotWriteableException($this->baseAbsolutePath);
		}

		return $this;
	}


	/**
	 * @throws Exceptions\DirIsNotReadableException
	 */
	public function checkReadable(): static
	{
		if ($this->filesystem->isReadable($this->baseAbsolutePath) === false) {
			throw new Exceptions\DirIsNotReadableException($this->baseAbsolutePath);
		}

		return $this;
	}


	public function __toString(): string
	{
		return $this->getDir();
	}


	/**
	 * @throws Exceptions\IOException
	 */
	final protected static function makeHomeDir(string $path, Filesystem $filesystem, string $root = ''): string
	{
		if ($filesystem->isAbsolute($path) === false) {
			if ($root === '') {
				$root = self::slash(sys_get_temp_dir(), 'h4kuna');
			}
			$path = self::createDir(self::slash($root, $path), $filesystem); // intentionally here in condition branch
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
	private static function createDir(string $path, Filesystem $filesystem): string
	{
		try {
			$filesystem->createDir($path);
		} catch (Throwable $e) {
			throw new Exceptions\IOException($path, 0, $e);
		}

		return $path;
	}

}
