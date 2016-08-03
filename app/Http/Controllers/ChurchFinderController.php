<?php

namespace App\Http\Controllers;

use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use Torann\GeoIP\GeoIPFacade as GeoIP;
use Illuminate\Http\Request;

use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use App\Http\Requests;

//https://github.com/bradcornford/Googlmapper

//Considering WP site, using iframe to embed church-finder app.  
//<iframe width="100%" height="500px"  src="http://localhost:8888/church-finder/public/"></iframe>

class ChurchFinderController extends Controller
{
    public function test()
    {
    }

    public function index(Request $request)
    {
        $params = ['zoom' => 14, 'type' => 'HYBRID', 'marker' => false];
        $data = [
            'search' => $request->input('search'), 
            'msg' => null, 
            'distance' => $request->input('distance', 20),
            'params' => $params
        ];

        if (strlen($request->input('search')) > 0) {
            try {
                $location = Mapper::location($request->input('search'));
                Mapper::map($location->getLatitude(), $location->getLongitude(), $params);

                //find nearby churches, default 20km TODO make distance variable
                $locations = \App\Church::findChurchesNearLatLon($location->getLatitude(), $location->getLongitude(), $request->input('distance'));
                $data['msg'] = "Found " . count($locations) . " churches";

            } catch (\Exception $e) {
                $data['msg'] = "Sorry, we couldn't find that. Try another search?";
                $this->getDefaultLocation($params);
                $locations = [];
            }

        } else {
            $params['zoom'] = 5;//wide zoom to show churches all over the country
            $this->getDefaultLocation($params);
            //When not searching, show all churches to populate map.  TODO evaluate performance.
            $locations = \App\Church::all();
        }

        foreach ($locations as $l) {
            Mapper::informationWindow($l->latitude, $l->longitude, $l->name);
        }
        $data['locations'] = $locations;

        return view('ChurchFinderDemo', $data);
    }

    protected function getDefaultLocation($params)
    {
        $location = GeoIP::getLocation('124.140.43.71');//TODO don't hardcode IP in production
        Mapper::map($location['lat'], $location['lon'], $params); 
    }
}
