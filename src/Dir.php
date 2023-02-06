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

		return $this->absolutePath($name);
	}


	/**
	 * Add relative path from $baseAbsolutePath
	 * @example both is possible 'foo' or 'foo/bar'
	 */
	public function dir(string $path): static
	{
		$newDir = $this->absolutePath($path);
		FileSystem::createDir($newDir);

		return new static($newDir);
	}


	public function __toString()
	{
		return $this->getDir();
	}


	private function absolutePath(string $path): string
	{
		return "$this->baseAbsolutePath/$path";
	}

}
