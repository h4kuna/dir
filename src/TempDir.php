<?php declare(strict_types=1);

namespace h4kuna\Dir;

final class TempDir extends Dir
{
	public function __construct(string $baseAbsolutePath = '')
	{
		if ($baseAbsolutePath === '') {
			$baseAbsolutePath = sys_get_temp_dir() . '/h4kuna';
		}

		parent::__construct($baseAbsolutePath);
	}

}
