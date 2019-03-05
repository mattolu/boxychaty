<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Exception;
use App\Models\Member;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{/**
 * Custom parameters.
 *
 * @var \Symfony\Component\HttpFoundation\ParameterBag
 *
 * @api
 */
public $attributes;
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->hasHeader('Authorization')){
            return response()->json([
                'result'=>[
                    'message'=>'Authorization Header not found',
                    'status'=>401]], 401);
        }
        $token = $request->bearerToken();
        $token = $request->get('token');
        $token = $request->header('Authorization');
        $token = substr($token, 7);
        if ($request->header('Authorization') == null || $token == null){
            return  reponse()->json([
                'result'=>[
                    'message'=>'No token provided', 
                    'status'=>401]], 401);
        }
            if (!$token) {
                return response()->json([
                'result'=>[
                    'message'=>'No token provided', 
                    'status'=>401]], 401);
            }
          
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS512']);
                }catch (ExpiredException $e) {
                    return response()->json([
                        'result'=>[
                            'message'=>'Provided token is expired.', 
                            'status'=>401]], 401);
                  
                } catch (\Exception $e) {
                    
                    return response()->json([
                        'result'=>[
                            'message'=>'Error while decoding', 
                            'status'=>401]], 401);
                
                }

                $member = Member::find($credentials->sub);
                $request->auth = $member;
                $request->attributes->add(['authUser' => $member]);
                
            
                return $next($request);
    }
}