<?php declare(strict_types=1);

namespace h4kuna\Dir;

use Nette\Utils\FileSystem;

final class TempDir extends Dir
{
	public function __construct(string $baseAbsolutePath = '')
	{
		if ($baseAbsolutePath === '' || FileSystem::isAbsolute($baseAbsolutePath) === false) {
			if ($baseAbsolutePath !== '') {
				$baseAbsolutePath = "/$baseAbsolutePath";
			}
			$baseAbsolutePath = sys_get_temp_dir() . "/h4kuna$baseAbsolutePath";
			FileSystem::createDir($baseAbsolutePath);
		}

		parent::__construct($baseAbsolutePath);
	}

}
