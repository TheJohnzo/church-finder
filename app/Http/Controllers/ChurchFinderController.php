<?php

namespace App\Http\Controllers;

use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use Torann\GeoIP\GeoIPFacade as GeoIP;
use Illuminate\Http\Request;

use App\Http\Requests;

//https://github.com/bradcornford/Googlmapper

//Considering WP site, using iframe to embed church-finder app.  
//<iframe width="100%" height="500px"  src="http://localhost:8888/church-finder/public/"></iframe>

class ChurchFinderController extends Controller
{
    public function test()
    {
        // \App\ChurchInfo::whereNotNull('id')->delete();
        // \App\ChurchAddressLabel::whereNotNull('id')->delete();
        // \App\ChurchAddress::whereNotNull('id')->delete();
        // \App\Church::whereNotNull('id')->delete();
        // //populate testing data
        // $data = [
            // ['name' => 'Pearl Vineyard', 'addr' => '横浜市南区井土ヶ谷中町１５７ ダイヤパレス井土ヶ谷２F ２W号室'],
            // ['name' => 'Yokohama International Baptist', 'addr' => '60 Nakaodai, Naka Ward, Yokohama, Kanagawa'],
            // ['name' => 'Yokohama Grace Bible Church', 'addr' => '5-85 Chojamachi, Naka Ward, Yokohama, Kanagawa Prefecture'],
            // ['name' => 'Yokohama Kaigan Church', 'addr' => '8 Nihonodori, Naka Ward, Yokohama'],
            // ['name' => 'カトリック山手教会', 'addr' => '44 Yamatecho, Naka Ward, Yokohama'],
            // ['name' => 'Tokyo Baptist Church', 'addr' => '9-2 Hachiyamacho, Shibuya, Tokyo'],
            // ['name' => 'Lifehouse Tokyo ライフハウス東京', 'addr' => 'Japan, 〒101-0062 Tokyo, 港区Roppongi, 7−18−18'],
            // ['name' => 'Iglesia Adventista Central de Tokio', 'addr' => '1-11-1 Jingumae, Shibuya, Tokyo 150-0001'],
            // ['name' => 'Tokyo LDS Temple', 'addr' => '5-8-10 Minamiazabu, Minato, Tokyo 106-0047, Japan'],
            // ['name' => 'Holy Resurrection Cathedral', 'addr' => 'Address: 4-1-3 Kanda Surugadai, Chiyoda, Tokyo 101-0062'],
            // ['name' => 'Kyoto International Church', 'addr' => '14 Saiinsanzocho, Ukyo Ward, Kyoto, Kyoto Prefecture, Japan'],
            // ['name' => '聖アグネス教会', 'addr' => 'Kyoto Prefecture, Kyoto 上京区堀松町404'],
            // ['name' => '北山バプテスト教会', 'addr' => '37 Kamigamo Iwagakakiuchicho, Kita Ward, Kyoto'],
            // ['name' => 'カトリック北白川教会', 'addr' => '22 Kitashirakawa Nishitsutacho, Sakyo Ward, Kyoto, Kyoto Prefecture'],
        // ];
        // foreach ($data as $d) {
            // \App\Church::insertFromNameAndAddress($d['name'], $d['addr']);
        // }
        \App\Church::updateLatLongFromAddressAll();
        \App\ChurchAddress::ifOnlyMakePrimaryAll();
    }

    public function index(Request $request)
    {
        $lang = ($request->lang) ? $request->lang : 'en';
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

                //find nearby churches, default 20km
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
            $locations = $locations->all();
        }

        foreach ($locations as $key => &$l) {

            $church_id = (isset($l->church_id)) ? $l->church_id : $l->id;
            $l->church_id = $church_id;//standardize for easier view
            $info = \App\ChurchInfo::where('church_id', $church_id)
                ->where('language', $lang)
                ->first();

            $address = \App\ChurchAddress::where('church_id', $church_id)
                ->where('primary', 1)
                ->first();
            if (!$address) {
                unset($locations[$key]);
                continue;//if no address, skip ahead
            }
            $addressLabel = \App\ChurchAddressLabel::where('church_address_id', $address['id'])
                ->where('language', $lang)
                ->first();
            Mapper::informationWindow($address['latitude'], $address['longitude'], $info['name']);

            //TODO better model to avoid adding adhoc params?
            $l->name = $info['name'];
            $l->lat = $address['latitude'];
            $l->long = $address['longitude'];
            $l->addr = $addressLabel['addr'];
        }
        $data['locations'] = array_values($locations);

        return view('ChurchFinderDemo', $data);
    }

    public function churchDetail($id, Request $request)
    {
        $church = \App\Church::findorfail($id);
        $data = [
            'church' => $church,
            'languages' => \App\Language::allIndexByCode(),
            'days' => $this->getDays(),
        ];
        return view('ChurchFinderDetail', $data);
    }

    protected function getDays()
    {
        //FIXME need multilingual solution
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    protected function getDefaultLocation($params)
    {
        $location = GeoIP::getLocation('124.140.43.71');//TODO don't hardcode IP in production
        Mapper::map($location['lat'], $location['lon'], $params); 
    }
}
