<?php

class MyWord{

    const SEPARATOR_TAB = "\t";

    /**
     * object zipArchive
     *
     * @var string
     * @access private
     */
    private $docx;

    /**
     * int 減少比例
     *
     * @var int
     * @access private
     */
    private $reduce = 1;

    /**
     * object domDocument from document.xml
     *
     * @var string
     * @access private
     */
    private $domDocument;

    /**
     * xml from document.xml
     *
     * @var string
     * @access private
     */
    private $_document;

    /**
     * xml from numbering.xml
     *
     * @var string
     * @access private
     */
    private $_numbering;

    /**
     *  xml from footnote
     *
     * @var string
     * @access private
     */
    private $_footnote;

    /**
     *  xml from endnote
     *
     * @var string
     * @access private
     */
    private $_endnote;

    /**
     * array of all the endnotes of the document
     *
     * @var string
     * @access private
     */
    private $endnotes;

    /**
     * array of all the footnotes of the document
     *
     * @var string
     * @access private
     */
    private $footnotes;

    /**
     * array of all the relations of the document
     *
     * @var string
     * @access private
     */
    private $relations;

    /**
     * array of characters to insert like a list
     *
     * @var string
     * @access private
     */
    private $numberingList;

    /**
     * the text content that will be exported
     *
     * @var string
     * @access private
     */
    private $textOuput;


    /**
     * boolean variable to know if a chart will be transformed to text
     *
     * @var string
     * @access private
     */
    private $chart2text;

    /**
     * boolean variable to know if a table will be transformed to text
     *
     * @var string
     * @access private
     */
    private $table2text;

    /**
     * boolean variable to know if a list will be transformed to text
     *
     * @var string
     * @access private
     */
    private $list2text;

    /**
     * boolean variable to know if a paragraph will be transformed to text
     *
     * @var string
     * @access private
     */
    private $paragraph2text;

    /**
     * boolean variable to know if footnotes will be extracteded
     *
     * @var string
     * @access private
     */
    private $footnote2text;

    /**
     * boolean variable to know if endnotes will be extracted
     *
     * @var string
     * @access private
     */
    private $endnote2text;

    /**
     * Construct
     *
     * @param $boolTransforms array of boolean values of which elements should be transformed or not
     * @access public
     */

    public function __construct($boolTransforms = array())
    {
        //table,list, paragraph, footnote, endnote, chart
        if (isset($boolTransforms['table'])) {
            $this->table2text = $boolTransforms['table'];
        } else {
            $this->table2text = true;
        }

        if (isset($boolTransforms['list'])) {
            $this->list2text = $boolTransforms['list'];
        } else {
            $this->list2text = true;
        }

        if (isset($boolTransforms['paragraph'])) {
            $this->paragraph2text = $boolTransforms['paragraph'];
        } else {
            $this->paragraph2text = true;
        }

        if (isset($boolTransforms['footnote'])) {
            $this->footnote2text = $boolTransforms['footnote'];
        } else {
            $this->footnote2text = true;
        }

        if (isset($boolTransforms['endnote'])) {
            $this->endnote2text = $boolTransforms['endnote'];
        } else {
            $this->endnote2text = true;
        }

        if (isset($boolTransforms['chart'])) {
            $this->chart2text = $boolTransforms['chart'];
        } else {
            $this->chart2text = true;
        }

        $this->textOuput = '';
        $this->docx = null;
        $this->_numbering = '';
        $this->numberingList = array();
        $this->endnotes = array();
        $this->footnotes = array();
        $this->relations = array();

    }

    /**
     *
     * Extract the content of a word document and create a text file if the name is given
     *
     * @access public
     * @param string $filename of the word document.
     *
     * @return string
     */

    public function extract($filename = '')
    {
        if (empty($this->_document)) {
            //xml content from document.xml is not got
            exit('There is no content');
        }

        $this->domDocument = new \DomDocument();
        $this->domDocument->loadXML($this->_document);//getElementsByTagName
        //get the body node to check the content from all his children
        $bodyNode = $this->domDocument->getElementsByTagName('body');
        //We get the body node. it is known that there is only one body tag
        $bodyNode = $bodyNode->item(0);
        foreach ($bodyNode->childNodes as $child) {
            //the children can be a table, a paragraph or a section. We only implement the 2 first option said.
            if ($this->table2text && $child->tagName == 'w:tbl') {
                //this node is a table and  the content is split with tabs if the variable table2text from the class is true
                $this->textOuput .= $this->table($child) . $this->separator();
            } else {
                //this node is a paragraph
                $this->textOuput .= $this->printWP($child) . ($this->paragraph2text ? $this->separator() : '');
            }
        }
        if (!empty($filename)) {
            $this->writeFile($filename, $this->textOuput);
        } else {
            return $this->textOuput;
        }
    }

    /**
     * Setter
     *
     * @access public
     * @param $filename
     */
    public function setDocx($filename)
    {
        $this->docx = new \ZipArchive();
        $ret = $this->docx->open($filename);
        if ($ret === true) {
            $this->_document = $this->docx->getFromName('word/document.xml');
        } else {
            throw new CHttpException(404,'文檔文件不存在或文檔無法打開，請與開發人員聯繫');
        }
    }

    public function closeFile(){
        $this->docx->close();
    }

    /**
     * extract the content to an array from endnote.xml
     *
     * @access private
     */
    private function loadEndNote()
    {
        if (empty($this->endnotes)) {
            if (empty($this->_endnote)) {
                $this->_endnote = $this->docx->getFromName('word/endnotes.xml');
            }
            if (!empty($this->_endnote)) {
                $domDocument = new \DomDocument();
                $domDocument->loadXML($this->_endnote);
                $endnotes = $domDocument->getElementsByTagName('endnote');
                foreach ($endnotes as $endnote) {
                    $xml = $endnote->ownerDocument->saveXML($endnote);
                    $this->endnotes[$endnote->getAttribute('w:id')] = trim(strip_tags($xml));
                }
            }
        }
    }

    /**
     * Extract the content to an array from footnote.xml
     *
     * @access private
     */
    private function loadFootNote()
    {
        if (empty($this->footnotes)) {
            if (empty($this->_footnote)) {
                $this->_footnote = $this->docx->getFromName('word/footnotes.xml');
            }
            if (!empty($this->_footnote)) {
                $domDocument = new \DomDocument();
                $domDocument->loadXML($this->_footnote);
                $footnotes = $domDocument->getElementsByTagName('footnote');
                foreach ($footnotes as $footnote) {
                    $xml = $footnote->ownerDocument->saveXML($footnote);
                    $this->footnotes[$footnote->getAttribute('w:id')] = trim(strip_tags($xml));
                }
            }
        }
    }

    /**
     * Extract the styles of the list to an array
     *
     * @access private
     */
    private function listNumbering()
    {
        $ids = array();
        $nums = array();
        //get the xml code from the zip archive
        $this->_numbering = $this->docx->getFromName('word/numbering.xml');
        if (!empty($this->_numbering)) {
            //we use the domdocument to iterate the children of the numbering tag
            $domDocument = new \DomDocument();
            $domDocument->loadXML($this->_numbering);
            $numberings = $domDocument->getElementsByTagName('numbering');
            //there is only one numbering tag in the numbering.xml
            $numberings = $numberings->item(0);
            foreach ($numberings->childNodes as $child) {
                $flag = true;//boolean variable to know if the node is the first style of the list
                foreach ($child->childNodes as $son) {
                    if ($child->tagName == 'w:abstractNum' && $son->tagName == 'w:lvl') {
                        foreach ($son->childNodes as $daughter) {
                            if ($daughter->tagName == 'w:numFmt' && $flag) {
                                $nums[$child->getAttribute('w:abstractNumId')] = $daughter->getAttribute('w:val');//set the key with internal index for the listand the value it is the type of bullet
                                $flag = false;
                            }
                        }
                    } elseif ($child->tagName == 'w:num' && $son->tagName == 'w:abstractNumId') {
                        $ids[$son->getAttribute('w:val')] = $child->getAttribute('w:numId');//$ids is the index of the list
                    }
                }
            }
            //once we know what kind of list there is in the documents, is prepared the bullet that the library will use
            foreach ($ids as $ind => $id) {
                if ($nums[$ind] == 'decimal') {
                    //if the type is decimal it means that the bullet will be numbers
                    $this->numberingList[$id][0] = range(1, 10);
                    $this->numberingList[$id][1] = range(1, 10);
                    $this->numberingList[$id][2] = range(1, 10);
                    $this->numberingList[$id][3] = range(1, 10);
                } else {
                    //otherwise is *, and other characters
                    $this->numberingList[$id][0] = array('*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*', '*');
                    $this->numberingList[$id][1] = array(chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175), chr(175));
                    $this->numberingList[$id][2] = array(chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237), chr(237));
                    $this->numberingList[$id][3] = array(chr(248), chr(248), chr(248), chr(248), chr(248), chr(248), chr(248), chr(248), chr(248), chr(248), chr(248));
                }
            }
        }
    }

    /**
     * Extract the content of a w:p tag
     *
     * @access private
     * @param $node object
     * @return string
     */
    private function printWP($node){
        $ret = "<p style='margin-bottom: 0px;";
        if($node->firstChild && $node->firstChild->firstChild){
            $arr = array();
            foreach ($node->firstChild->childNodes as $pFont){
                switch ($pFont->tagName){
                    case "w:spacing":
                        $lieHeightTamp = $pFont->getAttribute("w:line");
                        $arr[$pFont->tagName] = $pFont->getAttribute("w:lineRule")=="auto"?$this->reWidth($lieHeightTamp):$this->fontSize($lieHeightTamp);
                        break;
                    case "w:jc":
                        $arr[$pFont->tagName] = $pFont->getAttribute("w:val");
                        break;
                    case "w:pStyle":
                        $arr[$pFont->tagName] = $pFont->getAttribute("w:val");
                        break;
                    case "w:ind":
                        $arr[$pFont->tagName] = $pFont->getAttribute("w:firstLine");
                        break;
                    default:
                        $arr[$pFont->tagName] = $pFont->getAttribute("w:val");
                }
            }
            if(!empty($arr["w:spacing"])){
                $lineHeight = $arr["w:spacing"];
                //$ret.="line-height:$lineHeight;";
            }
            if(!empty($arr["w:jc"])){
                $ret.="text-align:".$arr["w:jc"].";";
            }
            if(!empty($arr["w:ind"])){
                $ret.="height:".$this->reWidth($arr["w:ind"]).";";
            }
        }
        $ret.="'>";
        foreach ($node->childNodes as $child){
            if($child->tagName == "w:r"){
                //var_dump($ret);
                $ret.="<span style='";
                foreach ($child->childNodes as $span){
                    if($span->tagName == "w:rPr"){
                        $arr =array();
                        foreach ($span->childNodes as $fonts){
							$sb = $fonts->tagName;
							$sb1 = $fonts->getAttribute("w:val");
                            $arr[$sb] = empty($sb1)?"aaa":$sb1;
                        }
                        if(!empty($arr["w:color"])){
                            $ret.=" color:".$arr["w:color"].";";
                        }
                        if(!empty($arr["w:b"])){
                            $ret.=" font-weight: bold;";
                        }
                        if(!empty($arr["w:i"])){
                            $ret.=" font-style:italic;";
                        }
                        if(!empty($arr["w:u"])){
                            $ret.=" text-decoration:underline;";
                        }
                        if(!empty($arr["w:sz"])){
                            $ret.=" font-size:".$this->fontSize($arr["w:sz"]).";";
                        }
                    }elseif ($span->tagName == "w:t" && $span->getAttribute("xml:space") == "preserve"){
                        $textLen = $span->textContent;
                    }
                }
                $ret.="'>";
                $ret.=preg_replace('/ /', '&nbsp;&nbsp;&nbsp;', htmlspecialchars($child->nodeValue));

                $ret.="</span>";
            }
        }
        $ret.="</p>";
        return $ret;
    }

    /**
     * return a text end of line
     *
     * @access private
     */
    private function separator()
    {
        return "\r\n";
    }

    /**
     *
     * Extract the content of a table node from the document.xml and return a text content
     *
     * @access private
     * @param $node object
     *
     * @return string
     */
    private function table($node)
    {
        $output = '<table class="table table-bordered">';
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                //start a new line of the table
                if ($child->tagName == 'w:tr') {
                    $height = $child->firstChild->firstChild->getAttribute("w:val");
                    $height = $this->reWidth($height);
                    $output.="<tr height='$height'>";
                    foreach ($child->childNodes as $cell) {
                        //start a new cell
                        if ($cell->tagName == 'w:tc') {
                            if ($cell->hasChildNodes()) {
                                $tdStr = "<td";
                                foreach ($cell->childNodes as $p) {
                                    if ($p->tagName=="w:tcPr"){
                                        //第一個節點
                                        $arr=array();
                                        foreach ($p->childNodes as $shen){
											$sb = $shen->tagName;
											$sb1 = $shen->getAttribute("w:val");
                                            switch ($shen->tagName){
                                                case "w:tcW":
                                                    $arr["width"] = $this->reWidth($shen->getAttribute("w:w"));
                                                    break;
                                                case "w:vMerge":
                                                    $arr[$sb] = empty($sb1)?"ok":$sb1;
                                                    break;
                                                case "w:vAlign":
                                                    $arr[$sb] = $sb1 == "center"?"middle":$sb1;
                                                    break;
                                                default:
                                                    $arr[$sb] = $sb1;
                                            }
                                        }
                                        //var_dump($arr);
                                        if(!empty($arr["width"])){
                                            $tdStr.=" width=".$arr["width"];
                                        }
                                        if(!empty($arr["w:gridSpan"])){
                                            $tdStr.=" colspan=".$arr["w:gridSpan"];
                                        }
                                        if(!empty($arr["w:vAlign"])){
                                            $tdStr.=" vAlign=".$arr["w:vAlign"];
                                        }
                                        if(!empty($arr["w:vMerge"])){
                                            $tdStr.=" reStart=".$arr["w:vMerge"];
                                        }

                                        $tdStr.=" >";
                                        $output.=$tdStr;
                                    }else{
                                        $output .= $this->printWP($p);
                                    }
                                }
                                $output .= self::SEPARATOR_TAB;
                                $output.="</td>";
                            }
                        }
                    }
                    $output.="</tr>";
                }
                $output .= $this->separator();
            }
        }
        $output.="</table>";
        return $output;
    }


    /**
     *
     * Extract the content of a node from the document.xml and return only the text content and. stripping the html tags
     *
     * @access private
     * @param $node object
     *
     * @return string
     */
    private function toText($node)
    {
        $xml = $node->ownerDocument->saveXML($node);
        return trim(strip_tags($xml));
    }

    private function fontSize($str){
        if (empty($str)){
            return "auto";
        }
        $num = intval($str)/2+2;
        return $num."px";
    }

    private function reWidth($str){
        if (empty($str)){
            return "auto";
        }
        $num = intval($str)*(1110/9696);
        $num = intval($num);
        return $num."px";
    }

    public function getDocumentXml(){
        $this->extract();
        return $this->domDocument;
    }
}