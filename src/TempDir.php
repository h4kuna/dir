<?php declare(strict_types=1);

namespace h4kuna\Dir;

use h4kuna\Dir\Storage\Filesystem;
use h4kuna\Dir\Storage\Local;

final class TempDir extends Dir
{
	public function __construct(string $baseAbsolutePath = '', ?Filesystem $filesystem = null)
	{
		$filesystem ??= new Local();
		parent::__construct(self::makeHomeDir($baseAbsolutePath, $filesystem), $filesystem);
	}

}
