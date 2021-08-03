<?php


namespace App\Repository\Storage;


trait HeroStorageTrait
{
    /**
     * @param string $filename
     * @param array $data
     *
     * @return false|int
     * @throws
     */
    public function storage(string $filename,array $data) :int|false
    {
        return file_put_contents(self::getStoragePath($filename),json_encode($data,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getStoragePath(string $filename) :string
    {
        $filename = BASE_PATH. '/storage/resources' . (strpos('/',$filename) ? $filename : ('/'.$filename));
        ResourceProvider::createDirectory(dirname($filename));
        return $filename;
    }
}