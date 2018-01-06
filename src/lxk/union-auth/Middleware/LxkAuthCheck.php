<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use App\Exceptions\ForbiddenException;
use Exception;
use App\LxkAuth;

class LxkAuthCheck
{
    protected $except = [
        //
        'root', 'logout'
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (count($this->except) > 0){
          foreach ($this->except as $route){
            if (\Route::current()->named($route)){
              \Log::debug("$route no need union_auth check;");
              return $next($request);
            }
          }
        }

        if (($token = $request->input('access_token', null)) && (LxkAuth::token() != $token)){
          \Log::debug("### token changed, logout");
          LxkAuth::logout();
        }
        // LOGIN
        if (!$user = LxkAuth::user()){
          \Log::debug("### user not in session");
          if ($token = $request->input('access_token', null)){
            \Log::debug("get token from url");
            // fetch user perms
            if (!$perms_url = config('lxk.services.auth.perms_url', null)){
              throw new Exception('Auth service not configured correctly.');
              return;
            }
            $perms = $this->perms(str_replace('{token}', $token, $perms_url));
            \Log::debug("get perms from $perms_url");
            \Log::debug(print_r($perms, 1));
            \Log::debug("set user/perms in session");
            // dd($perms);
            LxkAuth::token($token);
            LxkAuth::user($perms->user);
            LxkAuth::perms($perms->perms);
            // dd("save user in session");
          }else{
            if (!$login_url = config('lxk.services.auth.login_url', null)){
              throw new Exception('Auth service not configured correctly.');
              return;
            }

            return redirect($login_url . '?from='. $request->url());
          }
        }

        if (!LxkAuth::can_access()){
          throw new ForbiddenException('您没有相关权限。');
        }else{
          \Log::debug("route ".\Route::current()->uri()." can access");
        }

        return $next($request);
    }

    private function perms($perms_url)
    {
      $api = new HttpClient(['timeout' => 10]);

      $res = $api->get($perms_url);
      $text = $res->getBody()->getContents();
      $obj = json_decode($text);
      if ($res->getStatusCode() == 200 && $obj->success)
        return $obj->data;

      return null;
    }
}
