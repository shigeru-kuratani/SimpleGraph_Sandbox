<?php
/********************************************************************
* This file is part of SimpleGraph
/********************************************************************
* GraphBuilder.class
*
* PHP ver.5.x.x
*
* @package     graph
* @subpackage  builder
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
		require_once $baseDir . DIRECTORY_SEPARATOR . $className . '.class.php';
		return new $className($dataArray, $imageType);
	}
}
