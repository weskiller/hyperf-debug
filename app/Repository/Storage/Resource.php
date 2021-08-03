<?php

declare(strict_types=1);
/**
 *
 * @Copyright chongqing JiuWan Technology Co., Ltd
 *
 */

namespace App\Repository\Storage;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Resource\Json\JsonResource;
use Hyperf\Utils\Str;
use RuntimeException;

/**
 * Class Resource
 * @package App\Repository\Storage
 *
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property string $hash
 * @property int $size
 * @property string $uri
 * @property string $directory
 * @property string $saveJsonPath
 */
class Resource
{
    #[Inject]
    protected ResourceProvider $provider;

    /** @var array */
    protected array $info = [];

    /**
     * Resource constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->info['path'] = $path;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function load(array $data): self
    {
        return new static($data['path']);
    }


    /**
     * @param string $path
     * @return static
     */
    public static function create(string $path): self
    {
        return new static($path);
    }

    /**
     * @param $name
     *
     * @return mixed |null
     */
    public function __get($name)
    {
        if (isset($this, $name)) {
            return $this->{'get'.$name}();
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        throw new RuntimeException('set readonly property');
    }

    public function __isset($name): bool
    {
        return method_exists($this, 'get'.Str::studly($name));
    }

    /**
     * @return string | null
     */
    public function getExtension(): ?string
    {
        if (!isset($this->info['extension'])) {
            $this->info['extension'] = pathinfo($this->getPath(),
                PATHINFO_EXTENSION);
        }
        return $this->info['extension'];
    }

    public function getPath(): string
    {
        return $this->info['path'];
    }

    /**
     * @throws
     */
    public function getUri(): string
    {
        if (!isset($this->info['uri'])) {
            $this->info['uri'] = di()->get(ResourceProvider::class)
                ->toUri($this->getPath());
        }
        return $this->info['uri'];
    }

    public function getHash(): string
    {
        if (!isset($this->info['hash'])) {
            $this->info['hash'] = md5_file($this->getPath());
        }
        return $this->info['hash'];
    }

    public function getSize(): int
    {
        if (!isset($this->info['size'])) {
            $this->info['size'] = filesize($this->getPath());
        }
        return $this->info['size'];
    }

    public function getDirectory(): string
    {
        if (!isset($this->info['directory'])) {
            $this->info['directory'] = pathinfo($this->getPath(),
                PATHINFO_DIRNAME);
        }
        return $this->info['directory'];
    }

    /**
     * 销毁文件.
     */
    public function destroy(): void
    {
        if ($this->exists()) {
            if ($this->isFile()) {
                @exec(sprintf('rm -f "%s"', $this->info['path']));
            }
            if ($this->isDirectory()) {
                @exec(sprintf('rm -rf "%s"', $this->info['path']));
            }
        }
    }

    public function exists(): bool
    {
        return file_exists($this->getPath());
    }

    public function isFile(): bool
    {
        return is_file($this->info['path']);
    }

    public function isDirectory(): bool
    {
        return is_dir($this->info['path']);
    }

    public function display(): array
    {
        return JsonResource::make($this)->resolve();
    }
}
