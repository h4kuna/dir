<?php declare(strict_types=1);

namespace h4kuna\Dir;

use Nette;
use Nette\Utils\FileSystem;
use SplFileInfo;

/**
 * You don't fill last slash in path
 *
 * @phpstan-consistent-constructor
 */
class Dir
{
	public function __construct(private string $baseAbsolutePath, protected int $permissions = 0777)
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
	public function dir(string $path, ?int $permissions = null): static
	{
		$newDir = self::slash($this->baseAbsolutePath, $path);;

		return new static($this->createDir($newDir, $permissions), $permissions ?? $this->permissions);
	}


	/**
	 * @throws Exceptions\IOException
	 */
	public function create(?int $permissions = null): static
	{
		$this->createDir($this->baseAbsolutePath, $permissions);

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
	final protected function makeHomeDir(string $path, int $permissions, string $home = ''): string
	{
		if (FileSystem::isAbsolute($path) === false) {
			if ($home === '') {
				$home = self::slash(sys_get_temp_dir(), 'h4kuna');
			}
			$path = $this->createDir(self::slash($home, $path), $permissions); // intentionally here in condition branch
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
	private function createDir(string $path, ?int $permissions = null): string
	{
		if (is_dir($path)) {
			chmod($path, $permissions ?? $this->permissions);
			return $path;
		}

		try {
			FileSystem::createDir($path, $permissions ?? $this->permissions);
		} catch (Nette\IOException $e) {
			throw new Exceptions\IOException($path, 0, $e);
		}

		return $path;
	}

}
