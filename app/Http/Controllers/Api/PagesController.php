<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagesFindRequest;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{

    public function similar(Request $request)
    {
        try{
            if($json = @file_get_contents('https://app.linkhouse.co/rekrutacja/strony')) {
                $json = json_decode($json);
                $requested_site['site'] = $json->requested_site;
                $sites = json_decode(json_encode($json->sites), true);
                
                $order = $request->input('order') ?? ['quality']; //['traffic', 'quality', 'price']
                if(!is_array($order)) {
                    $order = [$order];
                }
                    
                foreach ($sites as $key=>$site) {
                    if ($site['site']==$requested_site['site']) {
                        $requested_site['traffic'] = $site['traffic'];
                        $requested_site['quality'] = $site['quality'];
                        $requested_site['price'] = $site['price'];
                        unset($sites[$key]);
                        break;
                    }
                }
                
                if (isset($requested_site['traffic'])) {
                    
                    usort($sites, fn ($a, $b): int =>
                        [
                            abs(($order[0]=='price' ? $a[$order[0]] : $b[$order[0]]) - $requested_site[$order[0]]),
                            @($order[1] ? abs(($order[1]=='price' ? $a[$order[1]] : $b[$order[1]]) - $requested_site[$order[1]]) : 0),
                            @($order[2] ? abs(($order[2]=='price' ? $a[$order[2]] : $b[$order[2]]) - $requested_site[$order[2]]) : 0),
                        ]
                        <=>
                        [
                            abs(($order[0]=='price' ? $b[$order[0]] : $a[$order[0]]) - $requested_site[$order[0]]),
                            @($order[1] ? abs(($order[1]=='price' ?  $b[$order[1]] : $a[$order[1]]) - $requested_site[$order[1]]) : 0),
                            @($order[2] ? abs(($order[2]=='price' ?  $b[$order[2]] : $a[$order[2]]) - $requested_site[$order[2]]) : 0),
                        ]
                        );

                    $result['success'] = true;
                    $result['requested_site'] = [
                        'site' => $requested_site['site'],
                        'traffic' => $requested_site['traffic'],
                        'quality' => $requested_site['quality'],
                        'price' => $requested_site['price'],
                    ];
                                       
                    $result['sites'] = array_slice($sites, 0, 10);
                    
                    return response()->json($result, 200);
                }                    
            }
            
            return response()->json(['success' => false, 'message' => 'Could not find data'], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
                
    }
       
    public function fill(Request $request)
    {
        try{
            if($json = @file_get_contents('https://app.linkhouse.co/rekrutacja/strony')) {
                
                Site::truncate();
                
                $json = json_decode($json);
                $sites = json_decode(json_encode($json->sites), true);
                                
                foreach ($sites as $site) {
                    Site::create([
                        'site' => $site['site'],
                        'traffic' => $site['traffic'],
                        'quality' => $site['quality'],
                        'price' => $site['price'],
                    ]);
                }           
                
                return response()->json(['success' => true, 'message' => 'Table site filled with data'], 200);
            }
            
            return response()->json(['success' => false, 'message' => 'Could not find data'], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }
    
    public function find(PagesFindRequest $request)
    {
        try{
            
            $sites = Site::select(['site', 'traffic', 'quality', 'price'])
            ->where([
                ['price', '<', $request->input('maxprice')],
                ['traffic', '>', $request->input('traficover')],
                ['quality', '>', $request->input('qualityover')],
            ])
            ->get()
            ->toArray();
            
            return response()->json($sites, 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
