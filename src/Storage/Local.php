<?php declare(strict_types=1);

namespace h4kuna\Dir\Storage;

use Nette\Utils;

final class Local implements Filesystem
{
	public function __construct(private int $mode = 0777)
	{
	}


	public function createDir(string $path): void
	{
		Utils\FileSystem::createDir($path, $this->mode);
	}


	public function isReadable(string $path): bool
	{
		return is_readable($path);
	}


	public function isWriteable(string $path): bool
	{
		return is_writeable($path);
	}


	public function isAbsolute(string $path): bool
	{
		return Utils\FileSystem::isAbsolute($path);
	}

}
