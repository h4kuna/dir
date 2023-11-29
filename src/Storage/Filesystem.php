<?php declare(strict_types=1);

namespace h4kuna\Dir\Storage;

interface Filesystem
{
	function createDir(string $path): void;


	function isReadable(string $path): bool;


	function isWriteable(string $path): bool;


	function isAbsolute(string $path): bool;
}
