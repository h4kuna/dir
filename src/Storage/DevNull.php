<?php declare(strict_types=1);

namespace h4kuna\Dir\Storage;

final class DevNull implements Filesystem
{
	public function createDir(string $path): void
	{
	}


	public function isReadable(string $path): bool
	{
		return true;
	}


	public function isWriteable(string $path): bool
	{
		return true;
	}


	public function isAbsolute(string $path): bool
	{
		return true;
	}

}
