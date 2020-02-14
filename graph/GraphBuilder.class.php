<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* @package     graph
* @class       GraphBuilder
* @subpackage  builder
* @author	   Shigeru Kuratani <kuratani_shigeru@benefiss.com>
* @copyright   2012, Shigeru Kuratani <Kuratani@benefiss.com>
* @license	   The BSD License
* @version	   1.1.0
* @link		   http://sg.benefiss.com
* @since	   File available since Release 1.0.0
********************************************************************/

class GraphBuilder
{	
	/**
	 * constructor
	 */
	public function __construct(){}
	
	/**
	 * destructor
	 */
	public function __destruct(){}
	
	/**
	 * factory method
	 * 
	 * @access public
	 * 
	 * @param  string $className class name of instance maked 
	 * @param  array  $dataArray data of graph
	 * @return object instance of $className
	 */
	public static function factory($className, $dataArray, $imageType)
	{
		$baseDir = dirname(__FILE__);
		require_once $baseDir . '/' . $className . '.class.php';
		return new $className($dataArray, $imageType);
	}
}
