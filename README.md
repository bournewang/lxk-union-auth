
# 蓝薪卡 联合认证

## install and config

- 安装lxk/auth composer,添加以下代码到composer.json中，运行composer update.
```
  "lxk/auth": ">=0.1"
```

- 分别在config/app.php中providers段和aliases段添加：

```
Lxk\LxkAuth::class
...
'LxkAuth' => Lxk\LxkAuth::class
```

- 在app/Http/Kernel.php， $routeMiddleware中添加：
```
'lxk.auth' => \Lxk\Middleware\LxkAuthenticate::class
```

- 在routes/web.php中，将所有的route加进下列组中：
```
Route::middleware('lxk.auth')->group(function(){
	Route::get('/home', 'HomeController@index')->name('home.home');
    ...
});
```

- 在.env中添加:
```
LXK_AUTH_SERVER=http://lxk-auth.local
SERVICE_CODE=lxk-sallery
```

- 在view中将Auth::user() 替换为LxkAuth::user()
- 将resources/views/layouts/app.blade.php中header的内容清空，使用lxk-auth的头部


# 在lxk-auth服务中配置此服务

```
    'lxk-sallery' => [
      'name' => '工资管理',
      'code' => 'lxk-sallery',
      'perm' => env('LXK_SALLERY').'/perms', // 权限列表
      'redirect_back' => env('LXK_SALLERY').'/home',
      'icon' => [
        'fa' => 'fa-comments',
      ],
      'home' => env('LXK_SALLERY').'/home',
      'logout' => env('LXK_SALLERY').'/logout/{token}',
    ],
```


