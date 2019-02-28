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
                'data'=>'Authorization Header not found', 
                'status'=>401], 401);
        }
        $token = $request->bearerToken();
        $token = $request->get('token');
        $token = $request->header('Authorization');
        $token = substr($token, 7);
        if ($request->header('Authorization') == null || $token == null){
            return  reponse()->json([
                'data'=>'No token provided', 
                'status'=>401], 401);
        }
            if (!$token) {
                return response()->json([
                'error' => 'Token not provided.'
                ], 401);
            }
          
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS512']);
                }catch (ExpiredException $e) {
                    return response()->json([
                    'error' => 'Provided token is expired.'
                    ], 400);
                } catch (\Exception $e) {
                    return response()->json([
                    'error' => 'Error while decoding'
                    ], 400);
                }

                $member = Member::find($credentials->sub);
                $request->auth = $member;
                $request->attributes->add(['authUser' => $member]);
                
            
                return $next($request);
    }
}