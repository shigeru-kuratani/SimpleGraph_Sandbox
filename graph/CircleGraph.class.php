<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* CircleGraph.class
*
* PHP ver.5.x.x
*
* @package     graph
* @subpackage  circlegraph
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
	public function __destruct()
	{
		parent::__destruct();
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
		$height = ($contentHeight >= $legendHeight) ? self::MARGIN_TOP + $contentHeight + self::MARGIN_BOTTOM : self::MARGIN_TOP + $legendHeight + self::MARGIN_BOTTOM;
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
			
			if ($value == 0) {
				$angle = 1;
			} else {
				$angle = ceil($value /  $this->_getSumValue($this->_dataArray) * 360);
			}
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
