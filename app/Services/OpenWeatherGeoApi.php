<?php


namespace App\Services;


class OpenWeatherGeoApi extends OpenWeatherApi
{
    protected ?string $endpoint = 'https://api.openweathermap.org/geo/1.0/direct?q={query}&limit={limit}&appid={appid}';
    protected int $limit = 5;
    protected ?string $query;

    protected function getParams(): array
    {
        $params = parent::getParams();
        $params['query'] = $this->getQuery();
        $params['limit'] = $this->getLimit();
        return $params;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @param string|null $query
     * @return self
     */
    public function setQuery(?string $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getResults(): ?array
    {
        if (null === $this->response_data) {
            $this->toResponse();
        }
        if ($this->response_data && is_array($this->response_data) && !empty($this->response_data)) {
            $node = $this->response_data[0];
            return [
                'name' => data_get($node, 'name'),
                'country' => data_get($node, 'country'),
                'lat' => data_get($node, 'lat'),
                'lon' => data_get($node, 'lon'),
            ];
        }
        return null;
    }

}