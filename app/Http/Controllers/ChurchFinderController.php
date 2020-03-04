<?php

namespace App\Http\Controllers;

use Mapper;
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
        $lang = ($request->lang) ? $request->lang : 'ja';
        \App::setLocale($lang);
        config(['googlmapper.language' => $lang]);
        $params = ['zoom' => 16, 'type' => 'HYBRID', 'marker' => false];
        $data = [
            'search' => $request->input('search'), 
            'msg' => null, 
            'distance' => $request->input('distance', 20),
            'params' => $params,
            'lang' => $lang,
        ];

        if (strlen($request->input('search')) > 0) {
            try {
                $location = Mapper::location($request->input('search'));
                Mapper::map($location->getLatitude(), $location->getLongitude(), $params);

                //find nearby churches, default 20km
                $locations = \App\Church::allChurchesNearLatLon($location->getLatitude(), $location->getLongitude(), $request->input('distance'));
                $locations = $locations->all();
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
            $infoText = view('map_info_window', ['info' => $info, 'l' => $l, 'church_id' => $church_id, 'lang' => $lang]);
            Mapper::informationWindow($address['latitude'], $address['longitude'], $infoText->render());

            //TODO better model to avoid adding adhoc params?
            $l->name = $info['name'];
            $l->lat = $address['latitude'];
            $l->long = $address['longitude'];
            $l->addr = $addressLabel['addr'];
        }
        $data['locations'] = array_values($locations);

        return view('church_finder_map', $data);
    }

    public function search(Request $request)
    {
        //TODO searchable church list based on location, tags, etc.
        $lang = ($request->lang) ? $request->lang : 'ja';
        \App::setLocale($lang);
        $tags = \App\TagTranslation::where('language', $lang)
            ->orderBy('tag')
            ->get();

        if (is_array($request->tags) && count($request->tags) > 0 && strlen($request->search) > 0) {
            $location = Mapper::location($request->input('search'));
            $locations = \App\Church::allChurchesNearLatLonTags($location->getLatitude(), $location->getLongitude(), $request->distance, $request->tags);
        } elseif (is_array($request->tags) && count($request->tags) > 0) {
            $locations = \App\Church::allChurchesByTags($request->tags);
        } elseif (strlen($request->search) > 0) {
            $location = Mapper::location($request->input('search'));
            $locations = \App\Church::allChurchesNearLatLon($location->getLatitude(), $location->getLongitude(), $request->distance);
        } else {
            $locations = \App\Church::all();
        }

        if (is_object($locations)) {
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

            //TODO better model to avoid adding adhoc params?
            $l->name = $info['name'];
            $l->addr = $addressLabel['addr'];
        }

        $data = [
            'locations' => array_values($locations),
            'languages' => \App\Language::allIndexByCode(),
            'lang' => $lang,
            'tag_translations' => $tags,
            'search' => $request->input('search'), 
            'distance' => $request->input('distance', 20),
            'tags' => $request->input('tags'),
        ];
        return view('church_finder_search', $data);
    }

    public function churchDetail($id, Request $request)
    {
        $lang = ($request->lang) ? $request->lang : 'ja';
        \App::setLocale($lang);
        $church = \App\Church::findorfail($id);
        $data = [
            'church' => $church,
            'languages' => \App\Language::allIndexByCode(),
            'lang' => $lang,
        ];
        return view('church_detail', $data);
    }

    public function organizationDetail($id, Request $request)
    {
        $lang = ($request->lang) ? $request->lang : 'ja';
        \App::setLocale($lang);
        $org = \App\Organization::findorfail($id);
        $data = [
            'org' => $org,
            'lang' => $lang,
        ];
        return view('organization_detail', $data);
    }

    protected function getDefaultLocation($params)
    {
        $location = \GeoIP::getLocation(\Request::ip());
        Mapper::map($location['lat'], $location['lon'], $params);
    }
}
