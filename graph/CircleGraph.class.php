<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* @package     graph
* @class       CircleGraph
* @subpackage  circlegraph
* @author	   Shigeru Kuratani <kuratani_shigeru@benefiss.com>
* @copyright   2012, Shigeru Kuratani <Kuratani@benefiss.com>
* @license	   The BSD License
* @version	   1.0.0
* @link		   http://sg.benefiss.com
* @since	   File available since Release 1.0.0
********************************************************************/

require_once dirname(__FILE__) . '/Graph.class.php';

class CircleGraph extends Graph
{
	
	const CIRCLE_RADIUS = 125; // radius of graph circle(px)
	
	/**
	 * constructor
	 * 
	 * @access public
	 *
	 * @param array $dataArray  data of graph
	 */
	public function __construct($dataArray = null, $imageType = null)
	{
		parent::__construct($dataArray, $imageType);
	}
	
	
	/**
	 * destructor
	 * 
	 * @access public
	 */
	public function __destrucnt()
	{
		parent::__destrucnt();
	}
	
	
	/**
	 * calculate width and height of graph image
	 * 
	 * @access protected
	 *
	 * @param int $dataArray  data of graph
	 * @return array [0] => width , [1] => length
	 */
	protected function _calcWidthAndHeightOfGraphImage($dataArray)
	{
		$dataCount = count($dataArray);
		$maxLengthOfLegend = 0;
		foreach ($dataArray as $key => $value) {
			if (mb_strlen($key, 'UTF-8') > $maxLengthOfLegend) {
				$maxLengthOfLegend = mb_strlen($key, 'UTF-8');
			}
		}
		
		// calculate width
		$imageMemoryWidth  = 0;
		$imageCircleWidth  = self::CIRCLE_RADIUS * 2;
		$imageLegendWidth  = self::CHARACTER_SIZE * 7 + self::CHARACTER_SIZE * ($maxLengthOfLegend * 1.3);
		$width = self::MARGIN_LEFT + $imageMemoryWidth + $imageCircleWidth + $imageLegendWidth + self::MARGIN_RIGHT;
		$width = ($width > self::MIN_GRAPH_WIDTH) ? $width : self::MIN_GRAPH_WIDTH;
		
		// calculate height
		$legendHeight  = (int)((self::CHARACTER_SIZE * 1.5) * $dataCount - (self::CHARACTER_SIZE * 0.5));
		$contentHeight = self::CIRCLE_RADIUS * 2;
		$height = ($contentHeight >= $contentHeight) ? self::MARGIN_TOP + $contentHeight + self::MARGIN_BOTTOM : 40 + $legendHeight + 40;
		$height = ($height > self::MIN_GRAPH_HEIGHT) ? $height : self::MIN_GRAPH_HEIGHT;
		
		return array($width, $height);
	}
		
	
	/**
	 * draw memory of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	protected function _drawMemory(){}
		
	
	/**
	 * draw circle of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	protected function _drawContent()
	{
		
		$circleTextColor = imagecolorallocate($this->_im, 255, 255, 255); // white
		
		// draw circle graph
		$start = 270;
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			
			$angle = ceil($this->_dataArray[$key] /  $this->_getSumValue($this->_dataArray) * 360);
			imagefilledarc($this->_im , self::MARGIN_LEFT + self::CIRCLE_RADIUS, self::MARGIN_TOP + self::CIRCLE_RADIUS,
				 	 	   self::CIRCLE_RADIUS * 2, self::CIRCLE_RADIUS * 2, $start, $start + $angle, $this->_colors[$i], IMG_ARC_PIE);
			
			$start += $angle;
			$i++;
		}
				
		$this->circleAreaWidth = self::MARGIN_LEFT + (self::CIRCLE_RADIUS * 2);
	}
	
	
	/**
	 * draw legend of graph
	 *
	 * @param void
	 * @return void
	 */
	protected function _drawLegend()
	{
		$legendAreaOffsetX = $this->circleAreaWidth + 10;
		$offsetY = self::MARGIN_TOP;
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			imagerectangle($this->_im, $legendAreaOffsetX, $offsetY, ($legendAreaOffsetX + self::CHARACTER_SIZE),
						  ($offsetY + self::CHARACTER_SIZE), $this->_colors[$i]);
			imagefilltoborder($this->_im, ($legendAreaOffsetX + 1), ($offsetY + 1), $this->_colors[$i], $this->_colors[$i]);
			
			$percentage = round($this->_dataArray[$key] /  $this->_getSumValue($this->_dataArray) * 100, 1);
			$writeText = $percentage . '% ' . $key;
			imagettftext($this->_im, self::CHARACTER_SIZE, 0, $legendAreaOffsetX + ceil(self::CHARACTER_SIZE * 1.5),
						 $offsetY + self::CHARACTER_SIZE, $this->_textColor, $this->_font, $writeText);
			
			$offsetY += ceil(self::CHARACTER_SIZE * 1.5);
			$i++;
		}
	}
	
}
