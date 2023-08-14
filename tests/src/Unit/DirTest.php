<?php declare(strict_types=1);

namespace h4kuna\Dir\Tests\Unit;

require __DIR__ . '/../../bootstrap.php';

use h4kuna\Dir\Dir;
use h4kuna\Dir\Exceptions\DirIsNotReadableException;
use h4kuna\Dir\Exceptions\DirIsNotWriteableException;
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


	public function testCheckWritealbe(): void
	{
		$dir = new Dir('/etc/foo');
		Assert::exception(fn () => $dir->checkWriteable(), DirIsNotWriteableException::class, '/etc/foo');

		$dir = new Dir(self::TEMP_DIR);
		Assert::same($dir, $dir->checkWriteable());
	}


	public function testCheckReadable(): void
	{
		$dir = new Dir('/etc/foo');
		Assert::exception(fn () => $dir->checkReadable(), DirIsNotReadableException::class, '/etc/foo');

		$dir = new Dir(self::TEMP_DIR);
		Assert::same($dir, $dir->checkReadable());
	}


	public function testFileInfo(): void
	{
		$dir = new Dir(self::TEMP_DIR);
		$fileInfo = $dir->fileInfo('file');

		Assert::type(\SplFileInfo::class, $fileInfo);
		Assert::same(self::TEMP_DIR . '/file', $fileInfo->getPathname());
	}


	public function testPermission(): void
	{
		$dir = new Dir(self::TEMP_DIR, 0755);
		$dir->create(0744);
		$path = $dir->getDir();
		Assert::same(0744, self::octal($path));

		$foo = $dir->dir('foo');
		$path = $foo->getDir();
		Assert::same(0755, self::octal($path));

		$foo2 = $foo->dir('foo', 0711);
		$path = $foo2->getDir();
		Assert::same(0711, self::octal($path));
	}


	private static function octal(string $path): int
	{
		return octdec(substr(sprintf('%o', fileperms($path)), -4));
	}

}

(new DirTest())->run();
