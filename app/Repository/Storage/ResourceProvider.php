<?php

declare(strict_types=1);
/**
 *
 * @Copyright chongqing JiuWan Technology Co., Ltd
 *
 */

namespace App\Repository\Storage;

use App\Concrete\System\Command;
use Hyperf\Config\Annotation\Value;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Utils\Collection;
use Hyperf\Utils\Str;
use JetBrains\PhpStorm\Pure;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use ZipArchive;

class ResourceProvider
{
    #[Value("storage.root")]
    protected string $root;

    #[Value("storage.prefix")]
    protected string $prefix;

    #[Value("storage.tmp")]
    protected string $tmp;

    /**
     * @param string $path
     *
     * @return array
     */
    public function relative(string $path): array
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        return [$this->root . $path, $this->prefix . $path];
    }

    /**
     * @param array $files
     * @param string $flag
     * @return Collection
     * @throws RuntimeException
     */
    public function saves(array $files, string $flag): Collection
    {
        $resources = collect();
        foreach ($files as $file) {
            $resources->push($this->uploadSave($file, $flag));
        }
        return $resources;
    }

    /**
     * @param UploadedFile $file
     * @param string $flag
     * @return mixed
     * @throws RuntimeException
     */
    public function uploadSave(UploadedFile $file, string $flag)
    {
        $extension = $file->getExtension();
        [$path] = $this->hashPathBuilder(md5_file($file->getRealPath()), $extension, $flag);
        $resource = new Resource($path);
        $file->moveTo($path);
        if (!$file->isMoved()) {
            throw new RuntimeException("Upload save failed $path");
        }
        return $resource;
    }

    /**
     * @param string $directory
     *
     * @return bool
     * @throws RuntimeException
     */
    public static function createDirectory(string $directory): bool
    {
        if (file_exists($directory)) {
            if (is_dir($directory)) {
                return true;
            }
            throw new RuntimeException("{$directory} exist but not directory");
        }
        $command = di()->get(Command::class)->exec(sprintf('mkdir -p "%s"',$directory));
        if (!$command->isSuccess()) {
            throw new RuntimeException("create directory {$directory} failed");
        }
        return true;
    }

    /**
     * @param string $path
     * @param string $src
     * @param bool $recursive
     *
     * @return bool
     * @throws RuntimeException
     */
    public static function copy(string $path,string $src,bool $recursive = false): bool
    {
        self::createDirectory(dirname($path));
        return di()->get(Command::class)->exec(sprintf('cp %s "%s" "%s"',$recursive ? '-r' : '',$src,$path))->isSuccess();
    }

    /**
     * @param string $hash
     * @param string | null $extension
     * @param string|null $prefix
     *
     * @return string[]
     * @throws
     */
    public function hashPathBuilder(string $hash, ?string $extension = null, ?string $prefix = null): array
    {
        $dir = implode('/', str_split(Str::substr($hash, 0, 4), 2));
        $prefix = (env('APP_NAME') ?: 'unknown_app') . '/hash/' . ($prefix ?: '');
        self::createDirectory(sprintf('%s/%s/%s', $this->root, $prefix, $dir));
        $path = sprintf('/%s/%s/%s', $prefix, $dir, $hash);
        if ($extension) {
            $path .= '.' . $extension;
        }
        $path = str_replace('//', '/', $path);
        return [$this->root . $path, $this->prefix . $path];
    }

    public function validate(string $url): bool
    {
        $uri = self::cropProtocol($url);
        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }
        if (Str::startsWith($uri, '/resource')) {
            $absolutelyPath = "{$this->root}/" . Str::substr($uri, 10);
            return file_exists($absolutelyPath);
        }
        return false;
    }

    public static function cropProtocol(string $url): string
    {
        if (Str::startsWith($url, 'http')) {
            return preg_replace('#https?://[^/]+#i', '', $url);
        }
        return $url;
    }

    /**
     * @param string $path
     * @return string
     * @throws RuntimeException
     */
    public function toUri(string $path): string
    {
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        if (Str::startsWith($path, $this->root)) {
            return Str::replaceFirst($this->root, $this->prefix, $path);
        }
        throw new RuntimeException('invalid resource absolutely path');
    }

    /**
     * @param string $url
     * @return string
     * @throws RuntimeException
     */
    public function toPath(string $url): string
    {
        $uri = self::cropProtocol($url);
        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }
        if (Str::startsWith($uri, $this->prefix)) {
            return Str::replaceFirst($this->prefix, $this->root, $uri);
        }
        throw new RuntimeException('invalid resource relatively uri');
    }

    #[Pure]
    public function validatePath(string $path): bool
    {
        return Str::startsWith($path, $this->root) && file_exists($path);
    }

    /**
     * @param string $path
     * @param string $src
     *
     * @return string
     * @throws RuntimeException
     */
    public function saveResource(string $path,string $src) :string
    {
        $absolute = $this->root .'/'. env('APP_NAME') . (Str::startsWith($path,'/') ? null : '/') . $path;
        if(!self::copy($absolute,$src)) {
            throw new RuntimeException(sprintf('copy file %s to %s failed',$src,$absolute));
        }
        return $absolute;
    }

    /**
     * @param $archivePath
     * @param $path
     *
     * @return bool
     */
    public static function archive2Zip($archivePath,$path) :bool
    {
        $archive = new ZipArchive();
        $archive->open($archivePath,ZipArchive::CREATE|ZipArchive::OVERWRITE);
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        /**
         * @var string $name
         * @var SplFileInfo $file
         */
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($path) + 1);
                $archive->addFile($filePath, $relativePath);
            }
        }
        return $archive->close();
    }

    /**
     * @param $file
     * @param $path
     *
     * @return bool
     * @throws RuntimeException
     */
    public static function systemCommandZip($file,$path) :bool
    {
        $command = di()->get(Command::class);
        if(!$command->exec('which zip')->isSuccess()) {
            throw new RuntimeException('zip command not found');
        }
        return $command->exec(sprintf('zip -r "%s", "%s"',$file,$path))->isSuccess();
    }

    /**
     * @param $file
     * @param $path
     *
     * @return bool
     * @throws RuntimeException
     */
    public static function extractZip($file,$path) :bool
    {
        if(!file_exists($file)) {
            throw new RuntimeException(sprintf('zip file <%s> not found',$file));
        }
        $archive = new ZipArchive();
        $archive->open($file);
        return $archive->extractTo($path);
    }

}
