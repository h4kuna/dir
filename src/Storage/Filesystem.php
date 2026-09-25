<?php declare(strict_types = 1);

namespace h4kuna\Dir\Storage;

interface Filesystem
{

	public function createDir(string $path): void;

	public function isReadable(string $path): bool;

	public function isWriteable(string $path): bool;

	public function isAbsolute(string $path): bool;

}
