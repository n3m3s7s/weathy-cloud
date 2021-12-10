<?php


namespace App\Services;


use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

abstract class OpenWeatherApi
{
    protected ?string $endpoint;
    protected ?string $appid;
    protected $response;
    protected $response_data;
    protected bool $use_cache = false;
    protected int $cache_ttl = 60;

    public function __construct()
    {
        $this->appid = config('services.openweather.apiKey');
        $this->use_cache = (bool)config('services.openweather.cache.enabled', false);
        $this->cache_ttl = (int)config('services.openweather.cache.ttl', 60);
    }

    /**
     * @return string|null
     */
    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    /**
     * @param string|null $endpoint
     */
    public function setEndpoint(?string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string|null
     */
    public function getAppid(): ?string
    {
        return $this->appid;
    }

    protected function getParams(): array
    {
        return [
            'appid' => $this->appid
        ];
    }

    /**
     * @return bool
     */
    public function isUseCache(): bool
    {
        return $this->use_cache;
    }

    /**
     * @param bool $use_cache
     * @return self
     */
    public function setUseCache(bool $use_cache): self
    {
        $this->use_cache = $use_cache;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cache_ttl;
    }

    /**
     * @param int $cache_ttl
     * @return self
     */
    public function setCacheTtl(int $cache_ttl): self
    {
        $this->cache_ttl = $cache_ttl;
        return $this;
    }

    /**
     * @return string
     */
    protected function getUri(): string
    {
        $params = $this->getParams();
        $searches = $replaces = [];
        foreach ($params as $key => $value) {
            $searches[] = '{' . $key . '}';
            $replaces[] = $value;
        }
        return str_replace($searches, $replaces, $this->getEndpoint());
    }

    /**
     * @return array|mixed
     */
    protected function fetch()
    {
        if ($this->isUseCache()) {
            $cache_key = $this->getCacheKey();
            $cache_ttl = $this->getCacheTtl();
            $data = $this->cacheStore()->remember($cache_key, $cache_ttl, function () {
                return $this->callEndpoint();
            });
            $this->response_data = $data;
            return $data;
        }
        return $this->callEndpoint();
    }

    /**
     * @return array|mixed
     */
    private function callEndpoint()
    {        
        $this->response = Http::get($this->getUri());
        $this->response_data = $this->response->json();
        return $this->response_data;
    }

    /**
     * @return array|mixed
     */
    public function toResponse()
    {
        try {
            return $this->fetch();
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return md5($this->getUri());
    }

    /**
     * @return Repository
     */
    protected function cacheStore(): Repository
    {
        return cache()->driver(config('services.openweather.cache.driver', 'redis'))->tags('openweather');
    }
}
