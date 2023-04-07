<?php declare(strict_types=1);

namespace h4kuna\Dir\Tests\Unit;

require __DIR__ . '/../../bootstrap.php';

use h4kuna\Dir\Dir;
use h4kuna\Dir\TempDir;
use Tester\Assert;
use Tester\TestCase;

final class DirTest extends TestCase
{

	private const TEMP_DIR = __DIR__ . '/../../temp';


	protected function setUp()
	{
		$tempDir = self::TEMP_DIR;
		exec("rm -rf '$tempDir/'*");
	}


	public function testBasic(): void
	{
		$tempDir = new TempDir(self::TEMP_DIR);
		$barDir = $tempDir->dir('foo/bar');
		Assert::true(is_dir("$tempDir/foo/bar"));

		$file = $barDir->filename('baz', 'txt');
		touch($file);
		Assert::true(is_file($file));

		Assert::same("$tempDir/foo/bar", (string) $barDir);
	}


	public function testFilenameOnly(): void
	{
		$tempDir = new TempDir(self::TEMP_DIR);

		$file = $tempDir->filename('foo/bar/baz', 'txt');
		Assert::true(is_dir("$tempDir/foo/bar"));
		touch($file);
		Assert::true(is_file($file));

		Assert::same("$tempDir/foo/bar/baz.txt", $file);
	}


	public function testBadBehavior(): void
	{
		$dir = new Dir(self::TEMP_DIR . '/foo/foo');
		$clone = $dir->dir('');
		Assert::same(self::TEMP_DIR . '/foo/foo/', $clone->getDir());
		Assert::notSame($dir, $clone);
	}


	public function testCreate(): void
	{
		$dir = new Dir(self::TEMP_DIR . '/foo/foo');
		$clone = $dir->create();
		Assert::same(self::TEMP_DIR . '/foo/foo', $clone->getDir());
		Assert::same($dir, $clone);
	}


	public function testSysTempDir(): void
	{
		$tempDir = new TempDir();
		$sysTempDir = sys_get_temp_dir() . '/h4kuna';

		$tempDir->filename('foo/bar/baz', 'txt');
		Assert::true(is_dir("$sysTempDir/foo/bar"));
	}


	public function testSysTempDirSub(): void
	{
		$tempDir = new TempDir('bar');
		$sysTempDir = sys_get_temp_dir() . '/h4kuna';

		$tempDir->filename('foo/bar/baz', 'txt');
		Assert::true(is_dir("$sysTempDir/bar/foo/bar"));
	}

}

(new DirTest())->run();
