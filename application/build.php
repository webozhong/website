<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 模块定义
    'front' => [
        '__dir__'    => ['controller', 'model', 'view'],
        'model' => ['Article'],
        'view' => ['index','aggregateHome'],
        'controller' => ['index', 'Article'],
    ],
    'admin' =>[
        '__dir__' => ['model','view','controller','simplehtmldom'],
        'model' => ['Login','Aggregation','WeApp'],
        'view' => ['aggregationIndex','delete','edit','login','weAppIndex'],
        'controller' => ['Login','Aggregation']
    ],
    'api' =>[
        '__dir__' => ['model','controller'],
        'model' => [],
        'controller' => []
    ]

];