<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Facades\Response;
use Validator;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class MemberController extends Controller
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
    * Create a new token.
    *
    * @param \App\User $user
    * @return string
    */
    //Genetating token for the login and registration if required...
    protected function jwt(Member $member){
        $payload = [
                    'iss' => 'lumen-jwt', // Issuer of the token
                    'sub' => $member->id, // Subject of the token
                    'iat' => time(), // Time when JWT was issued.
                    'exp' => time() + 1 * 24 * 60 * 60 //3600*3600 // Expiration time of 1 day
                ];
        return JWT::encode($payload, env('JWT_SECRET'), 'HS512');
    }
    /**
    * Authenticate a user and return the token if the provided credentials are correct.
    *
    * @param \App\User $user
    * @return mixed
    */

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
        'username' => 'required|unique:members',
        'email' => 'required|email|unique:members',
        'password' => 'required'
    ]);
        if ($validator->fails()) {
            return response()->json([
                'result'=>[
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
                                    'member_data'=>$member
                                ]]);    
        
                
                }catch(\Illuminate\Database\QueryException $ex){
                return json_encode([
                    'result'=>[
                        'status'=>500,
                        'registered'=>false,
                        'message'=>$ex->getMessage()
                        ]]);  
            }
        }
    
    public function authenticate(Member $member)
    {
        $validator = Validator::make($this->request->all(), 
        [
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result'=>[
                    'success' => false,
                    'status' =>400,
                    'message' => $validator->errors()->all()
                        ]]);
        }
            $member = Member::where('username', $this->request->input('username'))->first();
            if (!$member) {
                return response()->json([
                    'result' =>[
                        'message' => 'Username does not exist. Kindly REGISTER now',
                        'status' => 400
                        ]]);
            }
            if (Hash::check($this->request->input('password'), $member->password)) {
                return response()->json([
                    'result'=> [
                        'success'=> true,
                        'message'=>'Successfully logged in',
                         'token' => $this->jwt($member),
                         'status' => 200
                 ]]);
                }
                return response()->json([
                'result'=>[
                    'message' => 'Oops, you just typed a wrong password',
                    'status' => 400
            ]]);
    }
    public function getMemberAll(){
      try{ 
      $member = Member::orderBy('id', 'desc')->get();
           if ( count($member) !=0  ){
           return json_encode([
           'result'=>[
                   'status'=>200,
                   'message'=>'only members',
                   'members'=>$member
               ]]);
           } else{
               return json_encode([
                   'result'=>[
                       'status'=> 401,
                       'message'=> 'No Registered members'
                   ]]);
           }
       } catch ( Exception $e){
               return json_encode([
                   'result'=>[
                       'status'=> 401,
                       'message'=> $e
                   ]]);
           }
    }
}