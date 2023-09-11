<?php declare(strict_types=1);

namespace h4kuna\Dir;

final class DevNull extends Dir
{

	public function checkWriteable(): static
	{
		return $this;
	}


	public function checkReadable(): static
	{
		return $this;
	}


	protected static function createDir(string $path): string
	{
		return $path;
	}

}
