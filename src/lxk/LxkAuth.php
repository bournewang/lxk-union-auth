<?php

namespace Lxk;
use Illuminate\Support\ServiceProvider;
use Route;

class LxkAuth extends ServiceProvider
{
  const USER = 'user';
  const PERMS = 'perms';
  const TOKEN = 'token';

  static public function user($user = null)
  {
    if ($user){
      return session([self::USER => $user]);
    }else{
      return session(self::USER, null);
    }
  }

  static public function perms($perms = null)
  {
    if ($perms){
      return session([self::PERMS => $perms]);
    }else{
      return session(self::PERMS, []);
    }
  }

  static public function token($token = null)
  {
    if ($token){
      return session([self::TOKEN => $token]);
    }else{
      return session(self::TOKEN, null);
    }
  }

  static public function logout_url()
  {
    if (!$logout_url = config('lxk.services.auth.logout_url', null))
      throw new \Exception('auth service not configured correctly.');

    return $logout_url;
    return str_replace('{token}', self::token(), $logout_url);
  }

  // current user can access current url
  static function can_access($route_name = null)
  {
    if (empty(self::perms()))
      return false;
    foreach (self::perms() as $route){
      if(!$route_name && Route::current()->named($route))
        return true;
      elseif ($route_name && $route == $route_name)
        return true;
      else
        ;
    }
    return false;
  }

  static public function logout()
  {
    session([self::USER  => null]);
    session([self::PERMS => null]);
    session([self::TOKEN => null]);
  }
}
