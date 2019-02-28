<?php

namespace App\Http\Controllers;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Annotation;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use function GuzzleHttp\json_encode;
// use App\Http\Controllers;
// use Illuminate\Support\Collection;

class ProcessorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

            public function annotateUrls(Request $request){
                //Setting the variables for the time and array context
               
                $processedList = array();

            //validating the urls entered
                $validator = Validator::make($request->all(), [
                    'urls' => 'required'
                ]);
                if ($validator->fails()){
                    return response()->json([
                        'error'=>[
                            'success' => false,
                            'status' =>400,
                            'message' => $validator->errors()->all()
                                ]]);
                } else{
             
                    $urlsString = $request->urls;
                    $urlArray = explode(',',$urlsString);

                    
                    ///Looping through the array of the urls input
                    foreach($urlArray as $url){
                        //$status = false;
                        $preparedFileName = '';
                        $annotation = new Annotation();
                        
                    
                        try{
                            $current_timestamp = Carbon::now()->timestamp;
                           // $currentDateTime = date('Y-m-d H:i:s');
                            $url = trim($url);
                            //The API URL for getting the information 
                          
                            
                            $preparedUrl = 'https://tag.ontotext.com/ces-en/extract?url='.$url;
                            $preparedFileName = basename($url).'_'.$current_timestamp.'.json';
                            $client = new \GuzzleHttp\Client();
                            $response = $client->request('GET', $preparedUrl);
                            $status = $response->getStatusCode();
                            if($status == 200){
                                $annotation_reponse = $response->getBody();
                                if ($annotation_reponse!=NULL){
                                    Storage::disk('local')->put($preparedFileName, $annotation_reponse);
                                    $storagePath = storage_path($preparedFileName);

                                    try{
                                    $status = 'OK';
                                    $annotation->url = $url;
                                    $annotation->filename =  $preparedFileName;
                                    $annotation->status = $status;
                                    $annotation->filelocation = $storagePath;
                                    $annotation->member_id = app('request')->get('authUser')->id;
                                    $annotation->save();
                                    
                                            
                                
                                }  catch(\Illuminate\Database\QueryException $ex){
                                        return json_encode([
                                            'status'=>500,
                                            'posted'=>false,
                                            'message'=>$ex->getMessage()
                                            ]);  
                                    }
                                    }
                                
                                else  return json_encode([
                                    'error' => [
                                        'annotation'=>'The response is empty. Check your internet',
                                        'status' => 403
                                    ],
                                 ]
                                   );
                        
                                }else{
                                    //Perhaps there was error and the status code is not 200
                                    try{

                                        $annotation = new Annotation();
                                        $status = 'Not OK';
                                        $annotation->url = $url;
                                        $annotation->filename =  null;
                                        $annotation->status = $status;
                                        $annotation->filelocation = null;
                                        $annotation->member_id = app('request')->get('authUser')->id;
                                        $annotation->save();
                                }   
                                catch(\Illuminate\Database\QueryException $ex){
                                    return json_encode([
                                        'status'=>500,
                                        'posted'=>false,
                                        'message'=>$ex->getMessage()
                                        ]);  
                                }
                            }
                        
                        } catch (\GuzzleHttp\Exception\ConnectException $e) {
                            //Catch the guzzle connection errors over here.These errors are something 
                            // like the connection failed or some other network error
                        
                            return json_encode([
                                    'error' => 'The response is empty. Check your internet',
                                    'annotation'=>'file(s) not created',
                                    'status' => 403
                                
                             ]
                                );
                        }

                    }
                   
                   // if($annotation->save()){
                        return $reponse = response()->json([
                                 'annotation' => [
                                     'status' => 200,
                                     'success'=>true,
                                     'message' => 'Annotation files created and details saved successfully'    
                                                 ]
                                                         ]);
                                 //}
        }
    }
    
}


