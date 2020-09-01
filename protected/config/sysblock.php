<?php
return array(
	'ops.YA03' => array(
			'validation'=>'isSalesSummaryApproved',
			'system'=>'ops',
			'function'=>'YA03',
			'message'=>Yii::t('block','Please complete Operation System - Sales Summary Report Approval before using other functions.'),
		),
	'ops.YA01' => array(
			'validation'=>'isSalesSummarySubmitted',
			'system'=>'ops',
			'function'=>'YA01',
			'message'=>Yii::t('block','Please complete Operation System - Sales Summary Report Submission before using other functions.'),
		),
	'hr.RE02' => array(
			'validation'=>'validateReviewLongTime',
			'system'=>'hr',
			'function'=>'RE02',
			'message'=>Yii::t('block','Please complete Personnel System - Appraisial before using other functions.'),
		),
	'sp.GA01' => array(
			'validation'=>'isCreditApproved',
			'system'=>'sp',
			'function'=>'GA01',
			'message'=>Yii::t('block','Please complete Academic Credit System - Credit Request Approval before using other functions.'),
		),
	'sp.GA04' => array(
			'validation'=>'isCreditConfirmed',
			'system'=>'sp',
			'function'=>'GA04',
			'message'=>Yii::t('block','Please complete Academic Credit System - Credit Request Confirmation before using other functions.'),
		),
);
?>