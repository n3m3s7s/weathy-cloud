<?php

namespace App\Console\Commands;

use App\Services\OpenWeatherGeoApi;
use Illuminate\Console\Command;

class GeolocateCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geolocate:city {city}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geolocate a given city through OpenWeather API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OpenWeatherGeoApi $api)
    {
        $city = $this->argument('city');
        $this->comment('Performing geolocation using city => ' . $city);
        $results = $api->setQuery($city)->getResults();
        if (null === $results) {
            $this->error('Cannot resolve given city, sorry');
            return -1;
        }
        foreach ($results as $key => $value) {
            $this->info("[$key] => $value");
        }
        return 0;
    }
}
