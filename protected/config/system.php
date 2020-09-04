<?php

return array(

    'drs'=>array(
        'webroot'=>'http://192.168.1.5/swoper',
        'name'=>'Daily Report',
        'icon'=>'fa fa-pencil-square-o',
    ),
    'acct'=>array(
        'webroot'=>'http://192.168.1.5/acct',
        'name'=>'Accounting',
        'icon'=>'fa fa-money',
    ),
    'ops'=>array(
        'webroot'=>'http://192.168.1.5/operation',
        'name'=>'Operation',
        'icon'=>'fa fa-gears',
    ),
    'hr'=>array(
        'webroot'=>'http://192.168.1.5/hr',
        'name'=>'Personnel',
        'icon'=>'fa fa-users',
    ),
    'gr'=>array(
        'webroot'=>'http://192.168.1.5/integral',
        'name'=>'Integral',
        'icon'=>'fa fa-cubes',
    ),
    'cy'=>array(
        'webroot'=>'http://192.168.1.5/charity',
        'name'=>'Charitable Credit',
        'icon'=>'fa fa-object-ungroup',
    ),
    'exa'=>array(
        'webroot'=>'http://192.168.1.5/examina',
        'name'=>'Examina',
        'icon'=>'fa fa-leaf',
    ),
    'sev'=>array(
        'webroot'=>'http://192.168.1.5/several',
        'name'=>'Several',
        'icon'=>'fa fa-leaf',
    ),
    'onlib'=>array(
        'webroot'=>'https://onlib.lbsapps.com/seeddms',
        'script'=>'remoteLoginOnlib',
        'name'=>'Online Library',
        'icon'=>'fa fa-book',
        'external'=>array(
            'layout'=>'onlib',
            'update'=>'saveOnlib',		//function defined in UserFormEx.php
            'fields'=>'fieldsOnlib',
        ),
    ),

    /*
        'apps'=>array(
            'webroot'=>'https://app.lbsgroup.com.tw/web',
            'script'=>'remoteLoginTwApp',
            'name'=>'Apps System',
            'icon'=>'fa fa-rocket',
            'external'=>true,
        ),
    */
);

?>
