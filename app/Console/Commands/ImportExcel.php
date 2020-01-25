<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Church;
use App\ChurchInfo;
use App\ChurchAddress;
use App\ChurchAddressLabel;
use App\ChurchOrganization;
use App\OrganizationInfo;

class ImportExcel extends Command
{
    /**
     * The fields we look for in import
     * TODO might need to do more checking or make dynamic  
     */
    protected $fields = ['zip', 'city', 'ku', 'org_name', 'church_name', 'contact_phone', 'fax', 'name_ja', 'name_eng', 'contact_email', 'church_url'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:xls {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from a specifically formatted XLS file.';

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
     * @return mixed
     */
    public function handle()
    {
        $fileWithPath = $this->argument('file');
        if ($fileWithPath) {

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($fileWithPath);
            $data = $spreadsheet->getActiveSheet()->toArray();
            if (count($data) > 0) {
                $firstRow = $data[0];
                unset($data[0]);
                $fieldMap = [];

                foreach ($firstRow as $key => $value) {
                    foreach ($this->fields as $f) {
                        if ($f == $value) {
                            $fieldMap[$key] = $f;
                        }
                    }
                }
var_dump($fieldMap);
//die('');
                $insert = [];
                foreach ($data as $key => $row) {
                    $arr = [];
                    foreach ($row as $key2 => $cell) {
                        if (isset($fieldMap[$key2])) {
                            $arr[$fieldMap[$key2]] = $cell;
                        }
                    }
                    $insert[] = $arr;
                }

                if (!empty($insert)) {
                    foreach ($insert as $i) {
                        var_dump($i);
                        echo strlen($i['church_name']), "!!";
                        if (strlen($i['church_name']) > 0) {

                            // find or create church by japanese name
                            echo "church info lookup|";
                            $churchInfo = ChurchInfo::findOrCreateByName($i['church_name'], $language="ja");

                            $church = Church::find($churchInfo->church_id);

                            echo "saving church|";
                            // save contact info to church church_name  tel fax name_ja name_eng    contact_email   church_url
                            $church->contact_phone = $i['contact_phone'];
                            $church->contact_email = $i['contact_email'];
                            $church->url = $i['church_url'];
                            $church->save();
                            # $church->contact_email = $i['fax']; #need to add fax number
                            # TODO do we care about Pastor name?  name_ja, name_en

                            if ($i['org_name'] != '') {
                                echo "finding org|";
                                $organization = OrganizationInfo::findOrCreateByName($i['org_name'], $language="ja");
                                echo "adding church to org|";
                                ChurchOrganization::addToOrganization($church->id, $organization->organization_id);
                            } else {
                                echo "no org|";
                            }
                            
                            echo 'New church: ' , $i['church_name'] , "\n";
                            
                            // parse address and add Japanese only
                            if ($i['ku'] != '' && $i['city'] != '' && $i['zip'] != '') {
                                echo "adding address|";
                                $addr = $i['ku'] . ', ' . $i['city'] . ' ' . $i['zip'];
                                $address = ChurchAddressLabel::findOrCreateByAddr($addr, $language='ja', $church->id);

                                echo "Attempting lat/long lookup $addr \n";
                                try {
                                    Church::updateLatLongFromAddressByChurchId($church->id);
                                } catch (\Cornford\Googlmapper\Exceptions\MapperArgumentException $e) {
                                    echo "no lat/lon found! " , $e->getMessage() , "\n";
                                }
                            } else {
                                echo "no address|";
                            }

                        } else {
                            echo "No name given. skipping... \n";
                            echo var_export($i, true) . "\n";
                        }
                        #map lookups
                    }
                }
            }
        }
        Church::updateLatLongFromAddressAll();
        ChurchAddress::ifOnlyMakePrimaryAll();
    }
}
