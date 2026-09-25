<?php declare(strict_types = 1);

namespace h4kuna\Dir;

use h4kuna\Dir\Exceptions\DirIsNotReadableException;
use h4kuna\Dir\Exceptions\DirIsNotWriteableException;
use h4kuna\Dir\Exceptions\IOException;
use h4kuna\Dir\Storage\Filesystem;
use h4kuna\Dir\Storage\Local;
use SplFileInfo;
use Stringable;
use Throwable;
use function basename;
use function dirname;
use function sys_get_temp_dir;

/**
 * You don't fill last slash in path
 *
 * @phpstan-consistent-constructor
 */
class Dir implements Stringable
{

	private Filesystem $filesystem;


	public function __construct(
		private string $baseAbsolutePath,
		?Filesystem $filesystem = null,
	)
	{
		$this->filesystem = $filesystem ?? new Local();
	}

	public function getDir(bool $addSlash = false): string
	{
		return $addSlash
			? self::slash($this->baseAbsolutePath, '')
			: $this->baseAbsolutePath;
	}

	/**
	 * Make absolute path with filename
	 *
	 * @param string $name doesn't start with slash
	 *
	 * @throws IOException
	 */
	public function filename(
		string $name,
		string $extension = '',
	): string
	{
		/** @var non-empty-string $path */
		$path = dirname($name);
		if ($path !== '.') {
			return $this->dir($path)->filename(basename($name), $extension);
		} elseif ($extension !== '') {
			$name .= ".$extension";
		}

		return self::slash($this->baseAbsolutePath, $name);
	}

	/**
	 * @throws IOException
	 */
	public function fileInfo(
		string $name,
		string $extension = '',
	): SplFileInfo
	{
		return new SplFileInfo($this->filename($name, $extension));
	}

	/**
	 * Add relative path from $baseAbsolutePath
	 *
	 * @param non-empty-string $path
	 *
	 * @throws IOException
	 * @example both is possible 'foo' or 'foo/bar'
	 */
	public function dir(string $path): static
	{
		$newDir = self::slash($this->baseAbsolutePath, $path);

		return new static(self::createDir($newDir, $this->filesystem), $this->filesystem);
	}

	/**
	 * @throws IOException
	 */
	public function create(): static
	{
		self::createDir($this->baseAbsolutePath, $this->filesystem);

		return $this;
	}

	/**
	 * @throws DirIsNotWriteableException
	 */
	public function checkWriteable(): static
	{
		if ($this->filesystem->isWriteable($this->baseAbsolutePath) === false) {
			throw new DirIsNotWriteableException($this->baseAbsolutePath);
		}

		return $this;
	}

	/**
	 * @throws DirIsNotReadableException
	 */
	public function checkReadable(): static
	{
		if ($this->filesystem->isReadable($this->baseAbsolutePath) === false) {
			throw new DirIsNotReadableException($this->baseAbsolutePath);
		}

		return $this;
	}

	public function __toString(): string
	{
		return $this->getDir();
	}

	/**
	 * @throws IOException
	 */
	final protected static function makeHomeDir(
		string $path,
		Filesystem $filesystem,
		string $root = '',
	): string
	{
		if ($filesystem->isAbsolute($path) === false) {
			if ($root === '') {
				$root = self::slash(sys_get_temp_dir(), 'h4kuna');
			}
			$path = self::createDir(self::slash($root, $path), $filesystem); // intentionally here in condition branch
		}

		return $path;
	}

	final protected static function slash(
		string $dir1,
		string $dir2,
	): string
	{
		return "$dir1/$dir2";
	}

	/**
	 * @throws IOException
	 */
	private static function createDir(
		string $path,
		Filesystem $filesystem,
	): string
	{
		try {
			$filesystem->createDir($path);
		} catch (Throwable $e) {
			throw new IOException($path, 0, $e);
		}

		return $path;
	}

}
