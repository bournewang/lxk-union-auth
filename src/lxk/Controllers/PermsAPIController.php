<?php

namespace Lxk\Controllers;

use Illuminate\Http\Request;
use Response;

/**
* Class AppVersionController
* @package App\Http\Controllers\API
*/

class PermsAPIController extends AppBaseController
{
  public function index()
  {
    $resource_names = [
      'home' => '首页',
      'root' => '/',
      'logout' => 'logout',
    ];
    $action_names = [
      'home' => '首页',
      'privacy_datas_items' => '分页隐私数据',
      'audit' => '审核',
      'audit_post' => '提交审核',
      'perms' => '权限',
      'store' => '新增',
      'index' => '列表',
      'create' => '创建',
      'destroy' => '删除',
      'update' => '更新',
      'show' => '查看',
      'edit' => '编辑'
    ];
    $data = [];
    foreach(\Route::getRoutes() as $route)
    {
      $route_array = [];
      $item = explode('.', $route->getName());
      \Log::info(json_encode($item));
      if (!$item[0])continue;

      if (!isset($data[$item[0]]))$data[$item[0]] = [ 'group' => $item[0], 'name' => (isset($resource_names[$item[0]]) ? $resource_names[$item[0]] : $item[0]), 'sub' => []];

      if(count($item) == 2){
        $data[$item[0]]['sub'][$item[1]] = ['route' => $route->getName(), 'name' => (isset($action_names[$item[1]]) ? $action_names[$item[1]] : $item[1])];
      }elseif (count($item) == 3) {
        # code...
        if (!isset($data[$item[0]]['sub'][$item[1]]))
        $data[$item[0]]['sub'][$item[1]] = [ 'group' => $item[1], 'name' => (isset($resource_names[$item[1]]) ? $resource_names[$item[1]] : $item[1]), 'sub' => []];;
        $data[$item[0]]['sub'][$item[1]]['sub'][] = ['route' => $route->getName(), 'name' => (isset($action_names[$item[2]]) ? $action_names[$item[2]] : $item[2])];
      }

    }
    return $this->sendResponse($data, 'get routes success');
  }

}
