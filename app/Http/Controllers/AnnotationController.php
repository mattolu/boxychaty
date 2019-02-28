<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use Illuminate\Support\Facades\Response;
use Validator;
use Illuminate\Http\Request;



class AnnotationController extends Controller
{
      /**
    * The request instance.
    *
    * @var \Illuminate\Http\Request
    */
    private $request;
    /**
    * Create a new controller instance.
    *
    * @param \Illuminate\Http\Request $request
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
  

    /**
    * Registration method
    *
    * @param Request $request registration request
    *
    * @return array
    * @throws \Illuminate\Validation\ValidationException
    */

 
    public function register(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'firstname' => 'required',
        'lastname' => 'required',
        'username' => 'required',
        'email' => 'required|email|unique:members',
        'password' => 'required'
    ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>[
                    'success' => false,
                    'status' =>400,
                    'message' => $validator->errors()->all()
                        ]]);
            }
            try{

                $hasher = app()->make('hash');

                $member = new Member();
                $member->firstname = $request->firstname;
                $member->lastname = $request->lastname;
                $member->username = $request->username;
                $member->email = $request->email;
                $member->password = $hasher->make($request->password);
              
                $member->save();
                return json_encode([
                            'result'=> [
                                    'success'=> true,
                                    'status'=>200,
                                    'message'=> 'Registration successful',
                                    'member_data'=>$member,
                                    //'token' => $this->jwt($user)
                                ]]);    
        
                
                }catch(\Illuminate\Database\QueryException $ex){
                return json_encode([
                    'status'=>500,
                    'registered'=>false,
                    'message'=>$ex->getMessage()
                    ]);  
            }
        }
    }