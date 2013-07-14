<?php
/**
* SMS BYJG - Componente para envio de SMS no CMS JOOMLA
*
* Ideia Original: Axel Sauerhoefer < mysms[at]quelloffen.com >
* SMS BYJG é uma modificação do SMS PIXXIS desenvolvido por Claudio Eden para Joomla 1.0
* http://www.byjg.com.br
*
* Todos os direitos reservados. 
*
* @license http://www.gnu.org/licenses/lgpl.html GNU/LGPL
* SMS BYJG eh um software livre. Esta versao pode ter sido modificado nos termos da 
* LGPL (Library ou Lesser General Public License), e como eh distribuida inclui ou eh derivado de 
* obras licenciado sob a Licenca Publica Geral GNU ou outras licencas de software livre ou open source
*
* Este programa e distribuido na esperanca que seja util, mas SEM QUALQUER GARANTIA, 
* sem mesmo a garantia implicita de COMERCIALIZACAO ou ADEQUACAO PARA UM DETERMINADO PROPOSITO.
*
**/

//check if joomla call us
defined( '_JEXEC' ) or die( 'Restricted access' );

if( defined( 'BYJG_BACKEND_TOOLBAR_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_TOOLBAR_PHP', 1 );

$html = dirname( __FILE__ ) . '/toolbar.byjg.html.php' ;
require_once( $html );

/**
 * ToolBar Dispatcher class
 *
 * Only a simple wrapper for toolbar html class
* @package byjg
* @subpackage Backend
*/
class ByJGToolBar
{
	/**
	 * Current task to execute
	 *
	 * @var string
	 */
	var $task;

	/**
	 * Currenct action
	 *
	 * @var action
	 */
	var $action;

	/**
	 * Html layer
	 */
	var $html;

	/**
	 * Constructor
	 *
	 * Get current task and action
	 */
	function ByJGToolBar()
	{
		$this->action	= JRequest::getVar( 'act',  '' );
		$this->task 	= JRequest::getVar( 'task', 'Default' );
		$this->html		= new ByJGToolBarHtml();
	}

	/**
	 * Execute
	 *
	 * Execute the toolbar
	 */
	function Execute()
	{
		if( is_null( $this->action ) ){
			return;
		}

		$method = 'Do' . ucfirst( strtolower( $this->action ) ) . ucfirst( strtolower( $this->task ) );

		if( !method_exists( $this, $method ) ){
			return;
		}

		if( !is_callable( array( $this, $method ) ) ){
			return;
		}

		call_user_func( array( $this, $method ) );
	}

	/**
	 * Show the default toolbar on provider panel
	 *
	 */
	function DoProviderDefault()
	{
		$this->html->ProviderDefault();
	}

	/**
	 * Show the edit toolbar on provider panel
	 *
	 */
	function DoProviderEdit()
	{
		$this->html->EditProvider();
	}

	/**
	 * Toolbar after save provider settings
	 */
	function DoProviderSave()
	{
		$this->html->ProviderDefault();
	}

	/**
	 * Show the user default toolbar
	 */
	function DoUserDefault()
	{
		$this->html->UserDefault();
	}

	/**
	 * Show the user default toolbar
	 */
	function DoUser()
	{
		$this->html->UserDefault();
	}

	/**
	 * Toolbar for editing a user
	 */
	function DoUserEdit()
	{
		$this->html->EditUser();
	}

	/**
	 * Advertisment default toolbar
	 */
	function DoAdDefault()
	{
		$this->html->AdDefault();
	}

	/**
	 * Default toolbar for global settings
	 */
	function DoGlobalDefault()
	{
		$this->html->GlobalDefault();
	}

	/**
	 * Default about
	 */
	function DoAboutDefault()
	{
		$this->html->AboutDefault();
	}

	/**
	 * Default about
	 */
	function DoPrereqDefault()
	{
		$this->html->AboutDefault();
	}
}

$toolbar = new ByJGToolBar();
$toolbar->Execute();
?>
