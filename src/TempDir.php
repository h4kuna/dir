<?php declare(strict_types=1);

namespace h4kuna\Dir;

final class TempDir extends Dir
{
	public function __construct(string $baseAbsolutePath = '')
	{
		parent::__construct(self::makeHomeDir($baseAbsolutePath));
	}

}
