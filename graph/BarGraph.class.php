<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* @package     graph
* @class       BarGraph
* @subpackage  bargraph
* @author	   Shigeru Kuratani <kuratani_shigeru@benefiss.com>
* @copyright   2012, Shigeru Kuratani <Kuratani@benefiss.com>
* @license	   The BSD License
* @version	   1.1.0
* @link		   http://sg.benefiss.com
* @since	   File available since Release 1.0.0
********************************************************************/

require_once dirname(__FILE__) . '/Graph.class.php';

class BarGraph extends Graph
{
	const BAR_WIDTH    = 20; // width of graph bars(px)
		
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
		$imageMemoryWidth = strlen((string)$this->_getMaxValue($this->_dataArray)) * self::CHARACTER_SIZE;
		$imageBarWidth = self::BAR_WIDTH * ($dataCount * 2 - 1);
		$imageLegendWidth  = self::CHARACTER_SIZE * 4 + self::CHARACTER_SIZE * ($maxLengthOfLegend * 1.3);
		$width = self::MARGIN_LEFT+ $imageMemoryWidth + $imageBarWidth + $imageLegendWidth + self::MARGIN_RIGHT;
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
	 * draw bars of graph
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
		
		$barX = $this->_memoryAreaWidth =
					strlen((string)$this->_getMaxValue($this->_dataArray)) * self::CHARACTER_SIZE + self::MARGIN_LEFT;
		
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			$barHeight = round(($this->_height - self::MARGIN_TOP - self::MARGIN_BOTTOM) * $value / $maxMemoryValue);
			$barY = self::MARGIN_TOP + ($this->_height - self::MARGIN_TOP - self::MARGIN_BOTTOM) - $barHeight;
			
			imagerectangle($this->_im, $barX, $barY, ($barX + self::BAR_WIDTH), ($this->_height - self::MARGIN_BOTTOM),
						   $this->_colors[$i]);
			imagefilltoborder($this->_im, ($barX + 1), ($barY + 1), $this->_colors[$i], $this->_colors[$i]);
			imagettftext($this->_im, self::CHARACTER_SIZE, 30, ($barX + 5), ($this->_height - 5),
						 $this->_textColor, $this->_font, (string)$value);
			
			$barX += (self::BAR_WIDTH * 2);
			$i++;
		}
		
		$this->_barAreaWidth = $barX - (self::BAR_WIDTH * 2);
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
		$legendAreaOffsetX = $this->_memoryAreaWidth + $this->_barAreaWidth;
		$offsetY = self::MARGIN_TOP;
		$i = 0;
		foreach ($this->_dataArray as $key => $value) {
			imagerectangle($this->_im, $legendAreaOffsetX, $offsetY, ($legendAreaOffsetX + self::CHARACTER_SIZE),
						  ($offsetY + self::CHARACTER_SIZE), $this->_colors[$i]);
			imagefilltoborder($this->_im, ($legendAreaOffsetX + 1), ($offsetY + 1), $this->_colors[$i], $this->_colors[$i]);
			
			imagettftext($this->_im, self::CHARACTER_SIZE, 0, $legendAreaOffsetX + ceil(self::CHARACTER_SIZE * 1.5),
						 $offsetY + self::CHARACTER_SIZE, $this->_textColor, $this->_font, $key);
			
			$offsetY += ceil(self::CHARACTER_SIZE * 1.5);
			$i++;
		}
	}
	
}
