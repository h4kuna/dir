<?php declare(strict_types=1);

namespace h4kuna\Dir\Tests\Unit;

require __DIR__ . '/../../bootstrap.php';

use h4kuna\Dir\DevNull;
use Tester\Assert;
use Tester\TestCase;

final class DevNullTest extends TestCase
{

	public function testBasic(): void
	{
		$dir = new DevNull('/any/path');

		// make nothing
		$dir->create()
			->checkWriteable()
			->checkReadable();

		Assert::same('/any/path', $dir->getDir());
		$foo = $dir->dir('foo');
		Assert::same('/any/path/foo', $foo->getDir());

		Assert::same('/any/path/bar/file.json', $dir->filename('bar/file', 'json'));
		Assert::same('/any/path/file.json', $dir->fileInfo('file', 'json')->getPathname());
	}

}

(new DevNullTest())->run();
