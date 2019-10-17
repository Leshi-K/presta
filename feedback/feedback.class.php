<?php
class Feedback
{
    public $id_students;
    public $date_born;
    public $average_mark;
    public $active;
    public $name;

	public static $definition = array(
	  'table' => 'feedback',
	  'primary' => 'id_feedback',
	  'multilang' => false,
	  'fields' => array(
	  	'title' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 300);
	  	'text'  => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 3000);
	  	'name'  => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100);
	    'date'  => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
	  ),
	);

    public function getRandomFeedback() {

        $sql = 'SELECT * FROM '._DB_PREFIX_.'`feedback` ORDER BY rand() LIMIT 1';

        return Db::getInstance()->executeS($sql);
    }

    public function insertFeedback($data) {

        $sql = 'INSERT INTO '._DB_PREFIX_.'`feedback` (name, title, text) VALUES ('.$data["name"].', '.$data["title"].', '.$data["text"].')';

        return Db::getInstance()->executeS($sql);
    }
}