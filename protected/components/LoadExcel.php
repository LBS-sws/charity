<?php

class LoadExcel {

    private $excelList;
    private $file_url;
	public function __construct($filePath) {
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        //spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $filePath = Yii::app()->basePath."/../".$filePath;
        $this->file_url = $filePath;
        $PHPExcel = new PHPExcel();
        $listHeader = array();
        $listBody = array();

        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return false;
            }
        }
        $PHPExcel = $PHPReader->load($filePath);
        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        $allColumn = $this->getColumnToNum($allColumn);
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        for($currentColumn= 0;$currentColumn<= $allColumn; $currentColumn++){
            $val = $currentSheet->getCellByColumnAndRow($currentColumn,1)->getValue();/**ord()将字符转为十进制数*/
            array_push($listHeader,$val);
        }
        /**从第二行开始输出，因为excel表中第一行为列名*/
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
            /**从第A列开始输出*/
            $arr = array();
            for($currentColumn= 0;$currentColumn<= $allColumn; $currentColumn++){
                //$value = $currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();
                //$f_value = $currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getFormattedValue();
                //$p_value = PHPExcel_Shared_Date::ExcelToPHP($value);
                //$val = $currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                //$val = $currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getCalculatedValue();//獲取公式后的結果
                $val = $currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getFormattedValue();//獲取公式后的結果
                array_push($arr,$val);
            }
            array_push($listBody,$arr);
        }
        $this->excelList =  array(
            "listHeader"=>$listHeader,
            "listBody"=>$listBody,
        );
    }

    public function getExcelList(){
        unlink($this->file_url);
        return $this->excelList;
    }

    private function getColumnToNum($str){
        if(strlen($str)==1){
            return ord($str)-65;
        }elseif(strlen($str)==2){
            $num = ord($str)-65;
            $num = 26*($num+1);
            $newStr = $str[1];
            $num += ord($newStr)-65;
            return $num;
        }
        return 60;
    }
}
?>