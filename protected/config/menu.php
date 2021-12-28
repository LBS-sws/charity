<?php

return array(
    'Charity Credit'=>array(
        'access'=>'CY',
		'icon'=>'fa-flag-checkered',
        'items'=>array(
            'Apply Charity Credit'=>array(
                'access'=>'CY01',
                'url'=>'/requestCredit/index',
            ),
            'Apply Charity Prize'=>array(
                'access'=>'CY02',
                'url'=>'/requestPrize/index',
            ),
        ),
    ),
    'Audit'=>array(
        'access'=>'GA',
		'icon'=>'fa-legal',
        'items'=>array(
            'Audit Charity Credit Staff'=>array(
                'access'=>'GA03',
                'url'=>'/auditCredit/index?type=1',
            ),
            'Audit Charity Credit'=>array(
                'access'=>'GA01',
                'url'=>'/auditCredit/index?type=2',
            ),
            'Audit Charity Prize'=>array(
                'access'=>'GA02',
                'url'=>'/auditPrize/index',
            ),
        ),
    ),
    'Search'=>array(
        'access'=>'SR',
        'icon'=>'fa-binoculars',
        'items'=>array(
            'Charity Credit Search'=>array(
                'access'=>'SR01',
                'url'=>'/searchCredit/index',
            ),
            'Charity Credit Search Sum'=>array(
                'access'=>'SR02',
                'url'=>'/searchSum/index',
            ),
            'Charity Prize Search'=>array(
                'access'=>'SR03',
                'url'=>'/searchPrize/index',
            )
        ),
    ),
    'System Setting'=>array(
        'access'=>'SS',
		'icon'=>'fa-gear',
        'items'=>array(
            'Charity Credit type'=>array(
                'access'=>'SS01',
                'url'=>'/creditType/index',
            ),
            'Charity Prize type'=>array(
                'access'=>'SS02',
                'url'=>'/prizeType/index',
            ),
        ),
    ),
    'Report'=>array(
        'access'=>'YB',
		'icon'=>'fa-file-text-o',
        'items'=>array(
            'Charity Credit Report'=>array(
                'access'=>'YB02',
                'url'=>'/report/creditslist',
            ),
            'Charity Prize Report'=>array(
                'access'=>'YB03',
                'url'=>'/report/prizelist',
            ),
            'Report Manager'=>array(
                'access'=>'YB01',
                'url'=>'/queue/index',
            ),
        ),
    ),
);
