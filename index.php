<?php
/**
 * User: dmxiao@enet.com.cn
 * Date: 2017/4/18
 * Time: 11:33
 * Filename: index.php
 */

require_once './vendor/autoload.php';
use App\XmlParser;

$data = file_get_contents('http://news.qq.com/newsgn/rss_newsgn.xml');
$xml = new XmlParser();
try{
    $xml->parseToArray($data);
    var_export($xml->getParent());
}catch (Exception $e){
    echo $e->getMessage();
}
