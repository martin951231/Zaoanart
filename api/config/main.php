<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module'
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true, // 启用美化URL
            'enableStrictParsing' => true, // 是否执行严格的url解析
            'showScriptName' => false, // 在URL路径中是否显示脚本入口文件

            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/site'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET qwe' => 'qwe',
                        'GET record_access' => 'record_access',
                        'GET up_pv_count2' => 'up_pv_count2',
                        'GET up_pv_count3' => 'up_pv_count3',
                        'GET up_pv_count4' => 'up_pv_count4',
                        'GET up_pv_count5' => 'up_pv_count5',
                        'GET up_pv_count6' => 'up_pv_count6',
                        'GET up_pv_count7' => 'up_pv_count7',
                        'GET up_pv_count8' => 'up_pv_count8',
                        'GET up_pv_count9' => 'up_pv_count9',
                        'GET up_pv_count10' => 'up_pv_count10',
                        'GET up_pv_count11' => 'up_pv_count11',
                        'GET up_pv_count12' => 'up_pv_count12',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/goods'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET findimage' => 'findimage',
                        'GET findgoodsa' => 'findgoodsa',
                        'GET findgoodsb' => 'findgoodsb',
                        'GET findgoodsc' => 'findgoodsc',
                        'GET findgoodsd' => 'findgoodsd',
                        'GET findgoodsall' => 'findgoodsall',
                        'GET findgoods_catagory' => 'findgoods_catagory',
                        'GET findgoodsown' => 'findgoodsown',
                        'GET category_find' => 'category_find',
                        'GET decoration' => 'decoration',
                        'GET singlestereo' => 'singlestereo',
                        'GET findmayimg' => 'findmayimg',
                        'GET findmayimgall' => 'findmayimgall',
                        'GET up_label' => 'up_label',
                        'POST to_shopcar' => 'to_shopcar',
                        'POST record_decoration' => 'record_decoration',
                        'GET getloginimg' => 'getloginimg',
                        'GET getaccessip' => 'getaccessip',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/category'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET findcategory' => 'findcategory',
                        'GET findcategory1' => 'findcategory1',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/theme'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET findtheme' => 'findtheme',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/label'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET findlabel' => 'findlabel',
                        'GET findlabel2' => 'findlabel2',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/border'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET get_border' => 'get_border',
                        'GET get_material' => 'get_material',
                        'GET get_series' => 'get_series',
                        'GET get_decoration_price' => 'get_decoration_price',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/register'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET register' => 'register',
                        'GET login' => 'login',
                        'GET getloginimg' => 'getloginimg',
                        'POST loginuser' => 'loginuser',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/code'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET sendcode' => 'sendcode',
                        'GET codetel' => 'codetel',
                        'GET verification' => 'verification',
                        'GET vercode' => 'vercode',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/weixin'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET getcate' => 'getcate',
                        'GET getcate2' => 'getcate2',
                        'GET getcate3' => 'getcate3',
                        'GET gettheme' => 'gettheme',
                        'GET gettheme2' => 'gettheme2',
                        'GET getallimg' => 'getallimg',
                        'GET getcateimg' => 'getcateimg',
                        'GET getthemeimg' => 'getthemeimg',
                        'GET getlabelimg' => 'getlabelimg',
                        'GET getsearchimg' => 'getsearchimg',
                        'GET getimg' => 'getimg',
                        'GET getlikeimage' => 'getlikeimage',
                        'GET getkeep' => 'getkeep',
                        'GET getkeepimg' => 'getkeepimg',
                        'GET getborderimg' => 'getborderimg',
                        'GET getborderseries' => 'getborderseries',
                        'GET decoration' => 'decoration',
                        'GET login' => 'login',
                        'GET getukeep' => 'getukeep',
                        'GET getukeep2' => 'getukeep2',
                        'GET getukeeps' => 'getukeeps',
                        'GET addkeep' => 'addkeep',
                        'GET getmykeep' => 'getmykeep',
                        'GET getusername' => 'getusername',
                        'GET getusername2' => 'getusername2',
                        'GET getusername3' => 'getusername3',
                        'GET getwxphone' => 'getwxphone',
                        'GET addnewkeep' => 'addnewkeep',
                        'GET updatekeep' => 'updatekeep',
                        'GET getcurrentkeep' => 'getcurrentkeep',
                        'GET getmyfocus' => 'getmyfocus',
                        'GET deletekeep' => 'deletekeep',
                        'GET getcate_theme' => 'getcate_theme',
                        'GET getimglabel' => 'getimglabel',
                        'GET add_attention' => 'add_attention',
                        'GET del_attention' => 'del_attention',
                        'GET add_attention_user' => 'add_attention_user',
                        'GET del_attention_user' => 'del_attention_user',
                        'GET getattentionuser' => 'getattentionuser',
                        'GET edit_username' => 'edit_username',
                        'POST edit_img' => 'edit_img',
                        'POST responsemsg' => 'responsemsg',
                        'POST veritytoken' => 'veritytoken',
                        'GET up_icon' => 'up_icon',
                        'GET deleteimg' => 'deleteimg',
                        'GET moveimg' => 'moveimg',
                        'GET copyimg' => 'copyimg',
                        'POST search_like' => 'search_like',
                        'GET message' => 'message',
                        'GET decrypttel' => 'decrypttel',
                        'GET jiashuiyin' => 'jiashuiyin',
                        'GET record_access_wechat' => 'record_access_wechat',
                        'GET upmysql' => 'upmysql',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/home'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'POST homeinfo' => 'homeinfo',
                        'POST up_username'=>'up_username',
                        'POST up_wechat'=>'up_wechat',
                        'POST up_address'=>'up_address',
                        'POST up_birthday'=>'up_birthday',
                        'POST up_newpwd'=>'up_newpwd',
                        'GET findkeep'=>'findkeep',
                        'GET finduserkeep'=>'finduserkeep',
                        'GET findkeep2'=>'findkeep2',
                        'GET userattenkeep'=>'userattenkeep',
                        'GET findattentionuser'=>'findattentionuser',
                        'GET del_attention_keep'=>'del_attention_keep',
                        'GET del_attention_user'=>'del_attention_user',
                        'GET add_attention_keep'=>'add_attention_keep',
                        'GET add_attention_user'=>'add_attention_user',
                        'GET userattentionuser'=>'userattentionuser',
                        'GET findkeepname'=>'findkeepname',
                        'GET findlunbotu'=>'findlunbotu',
                        'GET findkeepimg'=>'findkeepimg',
                        'GET findreckeep'=>'findreckeep',
                        'GET findkeepall'=>'findkeepall',
                        'GET addkeep'=>'addkeep',
                        'GET find_car'=>'find_car',
                        'POST to_excel'=>'to_excel',
                        'GET delete_keep'=>'delete_keep',
                        'GET select_keep'=>'select_keep',
                        'GET addto_keep'=>'addto_keep',
                        'GET set_history'=>'set_history',
                        'GET get_history'=>'get_history',
                        'GET getusername'=>'getusername',
                        'GET getusername1'=>'getusername1',
                        'GET move_img'=>'move_img',
                        'GET copy_img'=>'copy_img',
                        'GET delete_img'=>'delete_img',
                        'GET move_img_all'=>'move_img_all',
                        'GET copy_img_all'=>'copy_img_all',
                        'GET delete_img_all'=>'delete_img_all',
                        'GET record_access' => 'record_access',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/phphash'
                    ],
                    'pluralize'  => false,
                    'extraPatterns' => [
                        'GET isimagefilesimilar' => 'isimagefilesimilar',
                    ]
                ],

            ],

        ],
    ],
    'params' => $params,
];
