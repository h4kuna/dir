<?php declare(strict_types=1);

namespace h4kuna\Dir\Tests\Unit;

require __DIR__ . '/../../bootstrap.php';

use h4kuna\Dir\TempDir;
use Tester\Assert;
use Tester\TestCase;

final class DirTest extends TestCase
{

	public function testBasic(): void
	{
		$tempDir = __DIR__ . '/../../temp';
		exec("rm -rf '$tempDir/'*");
		$tempDir = new TempDir($tempDir);
		$barDir = $tempDir->dir('foo/bar');
		Assert::true(is_dir("$tempDir/foo/bar"));

		$file = $barDir->filename('baz', 'txt');
		touch($file);
		Assert::true(is_file($file));

		Assert::same("$tempDir/foo/bar", (string) $barDir);
	}


	public function testFilenameOnly(): void
	{
		$tempDir = __DIR__ . '/../../temp';
		exec("rm -rf '$tempDir/'*");
		$tempDir = new TempDir($tempDir);

		$file = $tempDir->filename('foo/bar/baz', 'txt');
		Assert::true(is_dir("$tempDir/foo/bar"));
		touch($file);
		Assert::true(is_file($file));

		Assert::same("$tempDir/foo/bar/baz.txt", $file);
	}


	public function testSysTempDir(): void
	{
		$tempDir = new TempDir();
		$sysTempDir = sys_get_temp_dir() . '/h4kuna';

		$file = $tempDir->filename('foo/bar/baz', 'txt');
		Assert::true(is_dir("$sysTempDir/foo/bar"));
	}

}

(new DirTest())->run();
