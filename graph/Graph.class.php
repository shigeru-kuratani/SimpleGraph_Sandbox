<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* Graph.class
*
* PHP ver.5.x.x
*
* @package     graph
* @subpackage  graph
* @author	   Shigeru Kuratani <kuratani@benefiss.com>
* @copyright   2012, Shigeru Kuratani <Kuratani@benefiss.com>
* @license	   The BSD License
* @version	   1.0.1
* @link		   http://sg.benefiss.com
* @since	   File available since Release 1.0.0
* @disclaimer  THIS SOFTWARE IS PROVIDED BY THE FREEBSD PROJECT ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*			         【邦訳】
*			         本ソフトウェアは、著作権者およびコントリビューターによって「現状のまま」提供されており、明示黙示を問わず、商業的な使用可能性、および特定の目的に対する適合性に関する暗黙の保証も含め、またそれに限定されない、いかなる保証もありません。著作権者もコントリビューターも、事由のいかんを問わず、 損害発生の原因いかんを問わず、かつ責任の根拠が契約であるか厳格責任であるか（過失その他の）不法行為であるかを問わず、仮にそのような損害が発生する可能性を知らされていたとしても、本ソフトウェアの使用によって発生した（代替品または代用サービスの調達、使用の喪失、データの喪失、利益の喪失、業務の中断も含め、またそれに限定されない）直接損害、間接損害、偶発的な損害、特別損害、懲罰的損害、または結果損害について、一切責任を負わないものとします。
********************************************************************/
abstract class Graph
{
	const MIN_GRAPH_WIDTH  = 300; // minimum width of graph image(px)
	const MIN_GRAPH_HEIGHT = 200; // minimum height of graph image(px)
	const CHARACTER_SIZE   = 10;  // size of character in graph image
	const MARGIN_TOP       = 25;  // graph image margin top
	const MARGIN_BOTTOM    = 30;  // graph image margin bottom
	const MARGIN_LEFT      = 10;  // graph image margin left
	const MARGIN_RIGHT     = 10;  // graph image margin right
	
	/**
	 * image resource
	 * @var resource
	 */
	protected $_im;
	
	/**
	 * graph image mime type
	 * @var string
	 */
	protected $_imageType;
	
	/**
	 * graph width
	 * @var int
	 */
	protected $_width;
	
	/**
	 * graph height
	 * @var int
	 */
	protected $_height;
	
	/**
	 * graph data
	 * @var array
	 */
	protected $_dataArray;
	
	/**
	 * bar colors
	 * @var array
	 */
	protected $_colors;
	
	/**
	 * text color
	 * @var int
	 */
	protected $_textColor;
	
	/**
	 * text font
	 * @var string
	 */
	protected $_font;
	
	/**
	 * width of memory area
	 * @var int
	 */
	protected $_memoryAreaWidth;
	
	/**
	 * width of content area
	 * @var int
	 */
	protected $_contentAreaWidth;
	
	/**
	 * width of legend area
	 * @var int
	 */
	protected $_legendAreaWidth;
	
	
	/**
	 * constructor
	 * 
	 * @access protected
	 *
	 * @param array $dataArray  data of graph
	 */
	protected function __construct($dataArray = null, $imageType = null)
	{
		if ($dataArray !== null) {
			$this->_dataArray = $dataArray;
			list($this->_width, $this->_height) =
			$this->_calcWidthAndHeightOfGraphImage($this->_dataArray);
		} else {
			$this->_width  = self::MIN_GRAPH_WIDTH;
			$this->_height = self::MIN_GRAPH_HEIGHT;
		}
	
		if ($imageType !== null) {
			$this->_imageType = $imageType;
		} else {
			$this->_imageType = 'png';
		}
	
		$this->_im = @imagecreate($this->_width, $this->_height);
		$backgroungColor = imagecolorallocate($this->_im, 255, 255, 255); // set backgroud color on white
	
		// make colors
		$this->_makeColors();
	
		$this->_textColor = imagecolorallocate($this->_im, 0, 0, 0);
	
		$this->_font = dirname(__FILE__) . '/font/ipagp.ttf';
	}
	
	
	/**
	 * destructor
	 * 
	 * @access protected
	 */
	protected function __destruct()
	{
		imagedestroy($this->_im);
	}
	
	
	/**
	 * calculate width and height of graph image
	 * 
	 * @access protected
	 *
	 * @param int $dataArray  data of graph
	 * @return array [0] => width , [1] => length
	 */
	abstract protected function _calcWidthAndHeightOfGraphImage($dataArray);
	
	
	/**
	 * get max value in dataArray
	 * 
	 * @access protected
	 *
	 * @param array $dataArray  data of graph
	 * @return int $maxValue  max value in dataArray
	 */
	protected function _getMaxValue($dataArray)
	{
		$val = 0;
		foreach ($dataArray as $value) {
			$val = ($val < $value) ? $value : $val;
		}
		return $val;
	}
	
	/**
	 * get sum value in dataArray
	 * 
	 * @access protected
	 *
	 * @param  array $dataArray  data of graph
	 * @return int $sumValue  sum of values in dataArray
	 */
	protected function _getSumValue($dataArray)
	{
		$val = 0;
		foreach ($dataArray as $value) {
			$val += $value;
		}
		return $val;
	}
	
	
	/**
	 * make colors
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	protected function _makeColors()
	{
		for($i = 0; $i < count($this->_dataArray); $i++) {
			switch ($i % 5) {
				case 0:
					$this->_colors[] = imagecolorallocate($this->_im, rand(200,255), rand(0,150), rand(0,150));
					break;
				case 1:
					$this->_colors[] = imagecolorallocate($this->_im, rand(0,150), rand(200,255), rand(0,150));
					break;
				case 2:
					$this->_colors[] = imagecolorallocate($this->_im, rand(0,150), rand(0,150), rand(200,255));
					break;
				case 3:
					$this->_colors[] = imagecolorallocate($this->_im, rand(100,200), rand(100,200), rand(100,200));
					break;
				case 4:
					$this->_colors[] = imagecolorallocate($this->_im, rand(0,150), rand(0,150), rand(0,150));
					break;
				default:
					$this->_colors[] = imagecolorallocate($this->_im, rand(0,150), rand(0,150), rand(0,150));
			}
		}
	}
	
		
	/**
	 * power
	 * 
	 * @access protected
	 * 
	 * @param int $base  fundamental number
	 * @param int $digit multiplier
	 */
	protected function _power($base, $digit)
	{
		$val = 1;
		for ($i = 0; $i < $digit; $i++) {
			$val *= $base;
		}
		return $val;
	}
	
	
	/**
	 * draw memory of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	abstract protected function _drawMemory();
	
	
	/**
	 * draw content of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	abstract protected function _drawContent();	
	
	
	/**
	 * draw legend of graph
	 * 
	 * @access protected
	 *
	 * @param void
	 * @return void
	 */
	abstract protected function _drawLegend();
	
	/**
	 * fetch binary code of graph image
	 * 
	 * @access public
	 * 
	 * @param  void
	 * @return binary code of image
	 */
	public function fetch()
	{
		ob_start();
		$this->flush();
		$graphImage = ob_get_contents();
		ob_end_clean();
		
		return $graphImage;
	}
	
	/**
	 * flush image of graph
	 * 
	 * @access public
	 * 
	 * @param  void
	 * @return void
	 */
	public function flush()
	{
		// draw memory of graph
		$this->_drawMemory();
		
		// draw contents of graph
		$this->_drawContent();
		
		// draw legend of graph
		$this->_drawLegend();
		
		// graph image flash
		if ($this->_imageType === 'png') {
			imagepng($this->_im);
		} elseif ($this->_imageType === 'jpeg') {
			imagejpeg($this->_im);
		} elseif ($this->_imageType === 'gif') {
			imagegif($this->_im);
		}
	}
}
