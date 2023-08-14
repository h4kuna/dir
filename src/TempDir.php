<?php declare(strict_types=1);

namespace h4kuna\Dir;

final class TempDir extends Dir
{
	public function __construct(string $baseAbsolutePath = '', int $permissions = 0777)
	{
		parent::__construct($this->makeHomeDir($baseAbsolutePath, $permissions), $permissions);
	}

}
