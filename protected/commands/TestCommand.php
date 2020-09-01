<?php
class TestCommand extends CConsoleCommand {
	protected $webroot;
	
	public function run($args) {
		
$zip = new clsTbsZip(); 		

$path1 = Yii::app()->basePath.'/commands/template/tc1.docx';
$path2 = Yii::app()->basePath.'/commands/template/tc2.docx';
$path3 = Yii::app()->basePath.'/commands/template/tc3.docx';
$path4 = Yii::app()->basePath.'/commands/template/tc4.docx';

// Open the first document
$zip->Open($path4);
$content4 = $zip->FileRead('word/document.xml');
$zip->Close();

// Extract the content of the first document
$p = strpos($content4, '<w:body');
if ($p===false) exit("Tag <w:body> not found in document 1.");
$p = strpos($content4, '>', $p);
$content4 = substr($content4, $p+1);
$p = strpos($content4, '</w:body>');
if ($p===false) exit("Tag </w:body> not found in document 1.");
$content4 = substr($content4, 0, $p);

// Open the first document
$zip->Open($path3);
$content3 = $zip->FileRead('word/document.xml');
$zip->Close();

// Extract the content of the first document
$p = strpos($content3, '<w:body');
if ($p===false) exit("Tag <w:body> not found in document 1.");
$p = strpos($content3, '>', $p);
$content3 = substr($content3, $p+1);
$p = strpos($content3, '</w:body>');
if ($p===false) exit("Tag </w:body> not found in document 1.");
$content3 = substr($content3, 0, $p);
$content3 .= $content4;

// Open the first document
$zip->Open($path2);
$content2 = $zip->FileRead('word/document.xml');
$zip->Close();

// Extract the content of the first document
$p = strpos($content2, '<w:body');
if ($p===false) exit("Tag <w:body> not found in document 1.");
$p = strpos($content2, '>', $p);
$content2 = substr($content2, $p+1);
$p = strpos($content2, '</w:body>');
if ($p===false) exit("Tag </w:body> not found in document 1.");
$content2 = substr($content2, 0, $p);
$content2 .= $content3;


// Insert into the second document
$zip->Open($path1);
$content1 = $zip->FileRead('word/document.xml');
$p = strpos($content1, '</w:body>');
if ($p===false) exit("Tag </w:body> not found in document 2.");
$content1 = substr_replace($content1, $content2, $p, 0);
$content1 = str_replace('${staffname}','Percy Lee',$content1);
$content1 = str_replace('${staffcode}','123456',$content1);
$content1 = str_replace('${staffgender}','Male',$content1);
$content1 = str_replace('${staffidno}','H12340494944',$content1);
$content1 = str_replace('${staffprov}','HK',$content1);
$content1 = str_replace('${staffaddress}','香港九龍新蒲崗大有街36號華興工業大廈9樓C座',$content1);
$zip->FileReplace('word/document.xml', $content1, TBSZIP_STRING);

// Save the merge into a third file
$zip->Flush(TBSZIP_FILE, 'merge.docx');
	}
}
?>