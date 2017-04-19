<?php
namespace App;
/**
 * User: dmxiao@enet.com.cn
 * Date: 2017/4/18
 * Time: 10:51
 * Filename: XmlParser.php
 */
class XmlParser
{
    private $parser;

    private $data;

    private $close_tag = false;

    private $error = array();
    private $parent;
    private $i=0;

    private $stack = array();
    private $seek;

    public function __construct(){
        $this->parser = xml_parser_create();
        var_dump($this->parser);
        xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
        xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
        xml_set_object($this->parser,$this);
        xml_set_element_handler($this->parser,'tag_open','tag_close');
        xml_set_character_data_handler($this->parser,'tag_data');
    }


    public function parseToArray($path){
        if(!xml_parse($this->parser,$path,true)){
            $this->error['column_number'] = xml_get_current_column_number($this->parser);
            $this->error['line_number'] = xml_get_current_line_number($this->parser);
            $this->error['message'] = xml_error_string(xml_get_error_code($this->parser));
            throw new Exception($this->error['message'],1);
        }
    }

    public function tag_open($parser,$tag_name,$tag_attr){
        if(!$this->parent){
            $this->parent['id'] = false;
        }
        $this->data[$this->i] = array('name'=>$tag_name,'parent'=>$this->parent['id']);
        $this->parent = array('id'=>$this->i);
        array_push($this->stack,$this->parent);
        $this->close_tag = false;
    }


    public function tag_close($parser,$tag_name){
        array_pop($this->stack);
        $this->parent = end($this->stack);
        $this->close_tag = true;
    }

    public function tag_data($parser,$str_data){
        if($this->close_tag){
            return true;
        }
        if(!isset($this->data[$this->i])){
            $this->i--;
            $this->data[$this->i]['data'] .= $str_data;
        }else{
            $this->data[$this->i]['data'] = $str_data;
        }
        $this->i++;
    }

    public function __destruct()
    {
        xml_parser_free($this->parser);
    }


    public function getParent(){

        $tree  = array();

        foreach ($this->data as $key => $item) {
            if(isset($this->data[$item['parent']]) && $item['parent'] !==false ){
                $this->data[$item['parent']][$item['name']][] = &$this->data[$key];
            }else{
                $tree[0][$item['name']][] = &$this->data[$key];
            }
        }

        $tree = $this->treeFormat($tree);
        return $tree;
    }


    public function treeFormat($tree){




    }


}