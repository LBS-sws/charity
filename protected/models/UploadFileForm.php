<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UploadFileForm extends CFormModel
{
	/* User Fields */
	public $file;
	public $path_url;
	public $file_name;
	public $id;
	public $type=0;

    public function attributeLabels()
    {
        return array(
            'file'=>Yii::t('contract','Attachment'),
        );
    }
	/**
     *jpg|gif|png|xlsx|xls|docx|doc|pdf|tif
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('file', 'file', 'types'=>'gif,jpg,jpeg,png,xlsx,xls,doc,docx,pdf,tif', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
	}

	public function getAttachmentList($index){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_attachment")
            ->where('id=:id', array(':id'=>$index))->queryRow();
        if($rows){
            $this->id = $rows["id"];
            $this->path_url = $rows["path_url"];
            $this->file_name = $rows["file_name"];
        }
        return $rows;
    }

    public function saveData()
    {
        $uid = Yii::app()->user->id;
        //記錄
        $id = Yii::app()->db->createCommand()->insert('hr_attachment', array(
            "path_url"=>$this->path_url,
            "file_name"=>$this->file_name,
            "lcu"=>$uid
        ));
        $this->id = Yii::app()->db->getLastInsertID();;
    }

    public function getFileList(){
	    $down_url = TbHtml::link("<span class='fa fa-download'></span>",Yii::app()->createUrl('employ/attachmentDown',array("index"=>$this->id)));
        $delete_url = TbHtml::link("<span class='fa fa-close'></span>","javascript:void(0);",
            array("class"=>"attachmentDelete","data-url"=>Yii::app()->createUrl('employ/attachmentDelete'),"data-id"=>$this->id,"data-type"=>$this->type)
        );
	    return array(
	        "id"=>$this->id,
            "down_url"=>$down_url,
            "delete_url"=>$delete_url,
	        "file_name"=>$this->file_name,
	        "path_url"=>$this->path_url,
	        "lcd"=>date('Y-m-d H:i:s'),
        );
    }

}
