<?php
/**
 * PHPWord
 *
 * Copyright (c) 2011 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 010 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    Beta 0.6.3, 08.07.2011
 */


/**
 * PHPWord_DocumentProperties
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 2009 - 2011 PHPWord (http://www.codeplex.com/PHPWord)
 */
class Template {
    
    /**
     * ZipArchive
     * 
     * @var ZipArchive
     */
    private $_objZip;
    
    /**
     * Temporary Filename
     * 
     * @var string
     */
    private $_tempFileName;
    
    /**
     * Document XML
     * 
     * @var string
     */
    private $_documentXML="";

    private $_path;
    
    /**
     * Create a new Template Object
     * 
     * @param string $strFilename
     */
    public function __construct($arr,$testBool=true,$contractBool=true,$city="") {
        $path = Yii::app()->basePath."/../upload/staff/";
        if (!file_exists($path)){
            mkdir ($path);
        }
        if(empty($city)){
            $city = Yii::app()->user->city();
        }
        $path = $path.$city."/";
        if (!file_exists($path)){
            mkdir ($path);
        }
        $this->_tempFileName = $path.time().".docx";
        $this->_path = $path;

        copy(Yii::app()->basePath."/../".$arr[0], $this->_tempFileName); // Copy the source File to the temp File

        $this->_objZip = new ZipArchive();
        $this->_objZip->open($this->_tempFileName);
        $this->_documentXML = $this->_objZip->getFromName('word/document.xml');
        $this->_documentXML = explode('<w:body>',$this->_documentXML,2)[0];
        $this->_documentXML.="<w:body>";
		$section = '';
        foreach ($arr as $key => $value){
            $bool = false;
            $objZip = new ZipArchive();
            $xml = new DomDocument();
            $url = Yii::app()->basePath."/../".$value;

            $objZip->open($url);
            $documentXML = $objZip->getFromName('word/document.xml');
//（以上二删一）
            $xmlObj = $xml->loadXML($documentXML);
            $timedom = $xml->getElementsByTagName("body");
            $timedom = $timedom->item(0);
            if($key != 0){
                $this->_documentXML.='<w:p><w:r><w:br w:type="page" /></w:r></w:p>';
            } 
            foreach ($timedom->childNodes as $dom){
                if($dom->tagName!="w:sectPr"){
                    $this->_documentXML.=$dom->ownerDocument->saveXML($dom);
                } else {
					if ($key==0) $section=$dom->ownerDocument->saveXML($dom);
				}
            }
			
            $objZip->close();
        }
        $this->_documentXML.=$section."</w:body></w:document>";
        $this->resetContractWord($testBool,$contractBool);
        //var_dump($this->_documentXML);die();
        //rsidRDefault
    }

    public function resetContractWord($testBool,$contractBool){
        if (strpos($this->_documentXML,'contractdeadline')!==false){
            $rprXml = explode('contractdeadline',$this->_documentXML,2)[0];
            $index = strripos($rprXml,"<w:rPr>");
            $count = $index-strlen($rprXml);
            $rprXml = substr($rprXml,$count);
        }else{
            return false;
        }
        $num = strlen($rprXml)-strlen('</w:rPr><w:t>');
        $rprXmlSingle = substr($rprXml,0,$num).'<w:u w:val="single"/></w:rPr><w:t>';
        $str ='</w:t></w:r><w:r>'.$rprXmlSingle;
        $str2 ='</w:t></w:r><w:r>'.$rprXml;
        //合同期限
        if($contractBool){
            $this->setValue("contractdeadline","有固定期限：从".$str."staffyears1".$str2."年".$str."staffmonth1".$str2."月".$str."staffday1".$str2."日起至".$str."staffyears2".$str2."年".$str."staffmonth2".$str2."月".$str."staffday2".$str2."日止。");
        }else{
            $this->setValue("contractdeadline","无固定期限：从".$str."staffyears3".$str2."年".$str."staffmonth3".$str2."月".$str."staffday3".$str2."日起至法定的终止条件出现时止。");
        }
        //試用期
        if($testBool){
            $this->setValue("testdeadline","试用期为".$str."stafftest".$str2."个月，从".$str."stafftestyears1".$str2."年".$str."stafftestmonth1".$str2."月".$str."stafftestday1".$str2."日起至".$str."stafftestyears2".$str2."年".$str."stafftestmonth2".$str2."月".$str."stafftestday2".$str2."日止，试用期工资为RMB ".$str."stafftestwage".$str2." 元/月。");
        }else{
            $this->setValue("testdeadline","无试用期。");
        }
    }


    /**
     * Set a Template value
     * 
     * @param mixed $search
     * @param mixed $replace
     */
    public function setValue($search, $replace) {
        //$search = '${'.$search.'}';

        $this->_documentXML = str_replace($search, $replace, $this->_documentXML);
    }

    /**
     * Set a Template value
     *
     * @param mixed $search
     * @param mixed $replace
     */
    public function getXMLString() {
        return $this->_documentXML;
    }
    
    /**
     * Save Template
     * 
     * @param string $strFilename
     */
    public function save($strFilename) {
        $strFilename = $this->_path.$strFilename.".docx";
        if(file_exists($strFilename)) {
            unlink($strFilename);
        }

        $this->_objZip->addFromString('word/document.xml', $this->_documentXML);
        
        // Close zip file
        if($this->_objZip->close() === false) {
            throw new Exception('Could not close zip file.');
        }
        
        rename($this->_tempFileName, $strFilename);
    }
}
?>