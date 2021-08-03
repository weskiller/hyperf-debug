<?php


namespace App\Repository\Collector;


use App\Concrete\Http\Client;
use App\Repository\Storage\ResourceProvider;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class Crawler
{
    public const StoragePath = BASE_PATH . '/storage/crawler/hero';

    public const UserAgent = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36'
    ];

    /**
     * @return Client
     */
    public function httpClient() :Client
    {
        return Client::retryClient(5)
            ->setLogger('collect:hero','crawler');
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws GuzzleException
     */
    public function getImage(string $url) :string
    {
        $info = pathinfo($url);
        $path = $this->storage(sprintf("images/%s/%s",md5($info['dirname']),$info['basename']));
        ResourceProvider::createDirectory(dirname($path));
        if(!file_exists($path)) {
            $this->httpClient()->get($url,[
                RequestOptions::HEADERS => [
                    'User-Agent' => array_rand(self::UserAgent),
                ],
                RequestOptions::SINK => $path,
            ]);
            return $path;
        }
        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function storage(string $path) :string
    {
        $path = self::StoragePath . (str_starts_with('/', $path) ? $path : ('/' . $path));
        ResourceProvider::createDirectory(dirname($path));
        return $path;
    }
}