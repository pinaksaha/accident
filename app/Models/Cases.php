<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class Cases extends Model
{
    protected $table = 'cases';


    /**
     * @return array
     * @throws \Exception
     */
    public static function getWorldReport()
    {
        // check if Cache exists
        $cache = Cache::get('world_report_active');

        if(empty($cache))
        {
            $sql = "select SUM(active) as active ,SUM(confirmed) as confirmed ,SUM(deaths) as deaths,SUM(recovered) as recovered from cases;";
            $data = DB::select($sql);
            $data =  $data[0];
            \cache(['world_report_active' => $data->active],now()->addHours(12));
            \cache(['world_report_confirmed' => $data->confirmed],now()->addHours(12));
            \cache(['world_report_deaths' => $data->deaths],now()->addHours(12));
            \cache(['world_report_recovered' => $data->recovered],now()->addHours(12));
        }

        return [
            'active' => \cache('world_report_active'),
            'confirmed' => \cache('world_report_confirmed'),
            'deaths' => \cache('world_report_deaths'),
            'recovered' => \cache('world_report_recovered'),
        ];
    }

    /**
     * @param $countryCode
     * @return array
     * @throws \Exception
     */
    public static function getCountryReport($countryCode)
    {
        if(empty($countryCode)){
            throw new \Exception('Country Code is Required');
        }
        $cache = Cache::get($countryCode."_report_active");
        if (empty($cache)) {
            $sql = "select SUM(cases.active) as active ,
                SUM(cases.confirmed) as confirmed ,
                SUM(cases.deaths) as deaths,
                SUM(cases.recovered) as recovered
                from cases
                join locations on locations.id = cases.`location_id`
                where locations.code ='" .$countryCode."'";
            $data = DB::select($sql);
            $data =  $data[0];
            \cache([$countryCode.'_report_active' => $data->active],now()->addHours(12));
            \cache([$countryCode.'_report_confirmed' => $data->confirmed],now()->addHours(12));
            \cache([$countryCode.'_report_deaths' => $data->deaths],now()->addHours(12));
            \cache([$countryCode.'_report_recovered' => $data->recovered],now()->addHours(12));
        }

        return [
            'active' => \cache($countryCode.'_report_active'),
            'confirmed' => \cache($countryCode.'_report_confirmed'),
            'deaths' => \cache($countryCode.'_report_deaths'),
            'recovered' => \cache($countryCode.'_report_recovered'),
        ];
    }
}
