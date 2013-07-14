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

if( defined( 'BYJG_FRONTEND_BYJG_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_FRONTEND_BYJG_PHP', 1 );

@session_start();

//setup correct language, and get all needed globals
global $Itemid;

$mainframe =& JFactory::getApplication();
$option = JRequest::getCmd('option');

$frontend_path = dirname( __FILE__ ) . '/';
$backend_path  = dirname( __FILE__ ) .'/../../administrator/components/com_byjg/';

DEFINE( '_BYJG_PATH' , 		$frontend_path );
DEFINE( '_BYJG_ADMIN_PATH' , 	$backend_path );

$obj  = &JFactory::getLanguage();	
$obj->load( 'com_byjg' );
	
require_once( $mainframe->getPath('front_html') );
require_once(_BYJG_ADMIN_PATH . 'provider/providerfactory.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.functions.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.user.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.group.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.error.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.phonebook.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.usergroups.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.config.php' );
require_once(_BYJG_ADMIN_PATH . 'byjg.crypt.php' );
require_once(_BYJG_PATH       . 'byjg.frontend.php' );

//check if user is registered
$user =& JFactory::getUser();

if( $user->get('id') < 1 )
{
	ByJGNoAuth();
	return;
}

//create our sms user object
$smsUser = new ByJGUser( $user->get('id') );

//check com_byjg user rights, is user allowed to send sms (backend)
if( $smsUser->isBlocked() == true )
{
  ByJGNoAuth();
  return;
}

$params = &JComponentHelper::getParams( 'com_component' );

//get task, setup default task to overview
$task = JRequest::getVar( 'task', 'default' );

$database =  &JFactory::getDBO();

jimport('joomla.html.pagination');
JHTML::_('behavior.mootools');

$params = array( 'ByJGUser'		=> $smsUser,
				 'mosParameters'	=> $params,
				 'mosMainframe'		=> $mainframe,
				 'mosDatabase'		=> $database,
				 'ItemId'		    => $Itemid,
				 'option'			=> $option,
				 'lang'				=> $lang
				 );

$frontend = new ByJGFrontend( $task, $params );

if( $frontend->CanHandle() )
{
	return $frontend->Execute();
}

echo JText::_('BYJG_ALERT_NOT_HANDLE_TASK') . $task;
?>
