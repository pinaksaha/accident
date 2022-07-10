<?php

namespace App\Console\Commands;

use App\Gateway\CoronaGateway;
use App\Models\Cases;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Location;

class PopulateLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populated Locations and Location Data';

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
     * @param $number
     * @return int
     */
    public function dataSanitize($number)
    {
        return empty($number) ? 0 : intval($number);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $gateway = new CoronaGateway($client);
        $data = $gateway->getConfirmedData();
        $records = json_decode($data,true);

        $this->output->progressStart(count($records));
        foreach ($records as $record) {
            $name = $record['provinceState'];
            if(empty($name))
            {
                $name = $record['countryRegion'];
            }
            $location = Location::where('name','=',$name)->first();
            if(empty($location))
            {
                $countryOfOrigin = null;
                if(!empty($record['provinceState']))
                {
                    // find the country
                    $countryOfOrigin = Location::where('name','=',$record['countryRegion'])->first();
                }
                $location = new Location([
                    'name' => $name,
                    'code' => $record['iso3'],
                    'long' => empty($record['long']) ? 0 : $record['long'],
                    'lat' => empty($record['lat']) ? 0 : $record['lat'],
                ]);
                if(!empty($countryOfOrigin))
                {
                    $location->belongs_to = $countryOfOrigin->id;
                }
                $location->save();
            }

            $case = Cases::where('location_id','=',$location->id)->first();
            if(empty($case)){
                $case = new Cases();
                $case->location_id = $location->id;
                $case->active = $this->dataSanitize($record['active']);
                $case->confirmed = $this->dataSanitize($record['confirmed']);
                $case->deaths = $this->dataSanitize($record['deaths']);
                $case->recovered = $this->dataSanitize($record['recovered']);
                $case->updated_stamp = $record['lastUpdate'];
                $case->number_of_cases_last_twenty_eight_days = $record['cases28Days'];
                $case->number_of_deaths_last_twenty_eight_days = $record['deaths28Days'];
                $case->save();
            } else {
                if($case->updated_stamp !== $record['lastUpdate']) {
                    $case->active = $this->dataSanitize($record['active']);
                    $case->confirmed = $this->dataSanitize($record['confirmed']);
                    $case->deaths = $this->dataSanitize($record['deaths']);
                    $case->recovered = $this->dataSanitize($record['recovered']);
                    $case->updated_stamp = $record['lastUpdate'];
                    $case->number_of_cases_last_twenty_eight_days = $record['cases28Days'];
                    $case->number_of_deaths_last_twenty_eight_days = $record['deaths28Days'];
                    $case->save();
                }
            }

        $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
