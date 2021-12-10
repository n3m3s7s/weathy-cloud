<?php


namespace App\Services;


use Illuminate\Http\Request;

class OpenWeatherOneCallApi extends OpenWeatherApi
{
    protected ?string $endpoint = 'https://api.openweathermap.org/data/2.5/onecall?lat={lat}&lon={lon}&units={units}&appid={appid}';
    protected string $units = 'metric';
    protected ?float $lat;
    protected ?float $lon;

    protected function getParams(): array
    {
        $params = parent::getParams();
        $params['lat'] = $this->getLat();
        $params['lon'] = $this->getLon();
        $params['units'] = $this->getUnits();
        return $params;
    }

    /**
     * @return string
     */
    public function getUnits(): string
    {
        return $this->units;
    }

    /**
     * @param string $units
     * @return self
     */
    public function setUnits(string $units): self
    {
        $this->units = $units;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * @param float|null $lat
     * @return self
     */
    public function setLat(?float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLon(): ?float
    {
        return $this->lon;
    }

    /**
     * @param float|null $lon
     * @return self
     */
    public function setLon(?float $lon): self
    {
        $this->lon = $lon;
        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function fillWithRequest(Request $request): self
    {
        $this->setLat((float)$request->get('lan'))->setLon((float)$request->get('lon'));
        return $this;
    }
}