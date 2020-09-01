<?php

class FormulaParser{
    private $_sourceStr;
    private $_resetStr;

    public function __construct($str=''){
        $this->_sourceStr = $str;
        $this->_resetStr = $str;
        $this->resetSourceStr();
    }

    public function setIFStr($str){
        $this->_sourceStr = $str;
        $this->_resetStr = $str;
        $this->resetSourceStr();
    }

    protected function resetSourceStr(){
        $str = $this->_resetStr;
        $str = str_replace("if","IF",$str);
        $str = str_replace("iF","IF",$str);
        $str = str_replace("If","IF",$str);
        if(!empty($str)){
            $oneNum = strpos($str,')');
            if ($oneNum!==false){
                $prevStr = substr($str,0,$oneNum);
                $nextStr = substr($str,$oneNum+1);
                $ifNum = strrpos($prevStr,'IF');
                if($ifNum!==false){
                    $prevIFStr = substr($prevStr,0,$ifNum);
                    $ifStr = substr($prevStr,$ifNum); //防止if後面有空格
                    $ifNum = strpos($ifStr,'(');//防止if後面有空格
                    $ifStr = substr($ifStr,$ifNum+1);
                    $boolNum = strrpos($ifStr,'(');
                    if($boolNum!==false){
                        $prevStr = $prevIFStr."IF(".substr($ifStr,0,$boolNum)."&lt;".substr($ifStr,$boolNum+1);
                        $nextStr = "&gt;".$nextStr;
                    }else{
                        $arr = explode(",",$ifStr);
                        $resetIF = $this->arrToStr($arr);

                        $prevStr = $prevIFStr.$resetIF;
                    }
                }
                $this->_resetStr = $prevStr.$nextStr;
                $this->resetSourceStr();
            }
        }else{
            $this->_resetStr = "";
        }
    }

    protected function arrToStr($arr){
        if(count($arr)!=3){
            return "";
        }else{
            $oneStr = $arr[0];
            if (strpos($oneStr,'==')===false&&strpos($oneStr,'!=')===false&&strpos($oneStr,'>=')===false&&strpos($oneStr,'<=')===false){
                $oneStr = str_replace("=","==",$oneStr);
            }
            $oneStr = str_replace("<>","!=",$oneStr);
            return "&lt;".$oneStr."?".$arr[1].":".$arr[2]."&gt;";
        }
    }

    public function getResetStr(){
        $str = $this->_resetStr;
        $str = str_replace("&lt;","(",$str);
        $str = str_replace("&gt;",")",$str);
        return $str;
    }

    public function getSourceStr(){
        return $this->_sourceStr;
    }
}
