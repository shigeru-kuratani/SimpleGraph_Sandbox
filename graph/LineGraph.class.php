<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* @package     graph
* @class       LineGraph
* @subpackage  linegraph
* @author	   Shigeru Kuratani <kuratani_shigeru@benefiss.com>
* @copyright   2012, Shigeru Kuratani <Kuratani@benefiss.com>
* @license	   The BSD License
* @version	   1.1.0
* @link		   http://sg.benefiss.com
********************************************************************/

require_once dirname(__FILE__) . '/Graph.class.php';

class LineGraph extends Graph
{
	
	const POINT_SIZE     = 4;  // size of line point
	const INTERVAL_WIDTH = 50; // interval of line bars(px)
		
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
			if (mb_strlen($value, 'UTF-8') > $maxLengthOfLegend) {
				$maxLengthOfLegend = mb_strlen($value, 'UTF-8');
			}
		}
		
		// calculate width
		$imageMemoryWidth = strlen((string)$this->_getMaxValue($this->_dataArray)) * self::CHARACTER_SIZE;
		$imageLineWidth = self::INTERVAL_WIDTH * ($dataCount - 1) + 25; // adapt to image size
		$imageLegendWidth  = self::CHARACTER_SIZE * 2 + self::CHARACTER_SIZE * ($maxLengthOfLegend * 1.3);
		$width = self::MARGIN_LEFT + $imageMemoryWidth + $imageLineWidth + $imageLegendWidth + self::MARGIN_RIGHT;
		$width = ($width > self::MIN_GRAPH_WIDTH) ? $width : self::MIN_GRAPH_WIDTH;
		
		// calculate height
		$legendHeight = (int)((self::CHARACTER_SIZE * 1.5) * $dataCount - (self::CHARACTER_SIZE * 0.5));
		$height = 40 + $legendHeight + 40;
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
	protected function _drawMemory()
	{
		$maxDigit = strlen((string)$this->_getMaxValue($this->_dataArray));
		$maxMemoryValue = ceil($this->_getMaxValue($this->_dataArray) / $this->_power(10, $maxDigit - 1))
						  * $this->_power(10, $maxDigit - 1);
		
		for ($i = 0; $i < $this->_memoryCount; $i++) {
			$value = round($maxMemoryValue / ($this->_memoryCount -1) * ($this->_memoryCount - 1 - $i));
			$offsetTop = floor(($this->_height - self::MARGIN_TOP - self::MARGIN_BOTTOM) / ($this->_memoryCount - 1)) * $i + self::MARGIN_TOP;
			imagettftext($this->_im, self::CHARACTER_SIZE, 0, self::MARGIN_LEFT, $offsetTop,
						 $this->_textColor, $this->_font, (string)$value);
		}
	}
		
	
	/**
	 * draw lines of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	protected function _drawContent()
	{
		$maxDigit = strlen((string)$this->_getMaxValue($this->_dataArray));
		$maxMemoryValue = ceil($this->_getMaxValue($this->_dataArray) / $this->_power(10, $maxDigit - 1))
						  * $this->_power(10, $maxDigit - 1);
		
		$lineX = $this->_memoryAreaWidth =
					strlen((string)$this->_getMaxValue($this->_dataArray)) * self::CHARACTER_SIZE + self::MARGIN_LEFT;
		
		$lineColor = imagecolorallocate($this->_im, 100, 100, 100); // line color
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			$lineHeight = round(($this->_height - self::MARGIN_TOP - self::MARGIN_BOTTOM) * $value / $maxMemoryValue);
			$lineY = self::MARGIN_TOP + ($this->_height - self::MARGIN_TOP - self::MARGIN_BOTTOM) - $lineHeight;
			
			if ($i > 0) {
				imageline($this->_im, $preLineX + round(self::POINT_SIZE / 2), $preLineY + round(self::POINT_SIZE / 2),
						$lineX + round(self::POINT_SIZE / 2), $lineY + round(self::POINT_SIZE / 2), $lineColor);
			}
			
			imagerectangle($this->_im, $lineX, $lineY, ($lineX + self::POINT_SIZE), ($lineY + self::POINT_SIZE),
						   $this->_colors[$i]);
			imagefilltoborder($this->_im, ($lineX + 1), ($lineY + 1), $this->_colors[$i], $this->_colors[$i]);
			imagettftext($this->_im, self::CHARACTER_SIZE, 30, $lineX, ($this->_height - 5),
						 $this->_textColor, $this->_font, (string)$key);
			
			$preLineX = $lineX;
			$preLineY = $lineY;
			$lineX += self::INTERVAL_WIDTH;
			$i++;
		}
		
		$this->_contentAreaWidth = $lineX - self::INTERVAL_WIDTH;
	}
	
	
	/**
	 * draw legend of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	protected function _drawLegend()
	{
		$legendAreaOffsetX = $this->_memoryAreaWidth + $this->_contentAreaWidth;
		$offsetY = self::MARGIN_TOP;
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			imagerectangle($this->_im, $legendAreaOffsetX, $offsetY, ($legendAreaOffsetX + self::CHARACTER_SIZE),
						  ($offsetY + self::CHARACTER_SIZE), $this->_colors[$i]);
			imagefilltoborder($this->_im, ($legendAreaOffsetX + 1), ($offsetY + 1), $this->_colors[$i], $this->_colors[$i]);
			
			imagettftext($this->_im, self::CHARACTER_SIZE, 0, $legendAreaOffsetX + ceil(self::CHARACTER_SIZE * 1.5),
						 $offsetY + self::CHARACTER_SIZE, $this->_textColor, $this->_font, $value);
			
			$offsetY += ceil(self::CHARACTER_SIZE * 1.5);
			$i++;
		}
	}
	
}
