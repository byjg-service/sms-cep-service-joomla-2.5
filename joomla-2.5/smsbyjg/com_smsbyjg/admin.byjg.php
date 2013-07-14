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

if( defined( 'BYJG_BACKEND_ADMIN_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_ADMIN_PHP', 1 );

$backend_path  = dirname( __FILE__ ) . '/';
DEFINE( '_BYJG_ADMIN_PATH' , 	$backend_path );

$obj  = &JFactory::getLanguage();	
$obj->load( 'com_byjg' );

require_once( _BYJG_ADMIN_PATH . 'byjg.functions.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.crypt.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.config.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.error.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.backend.php' );
require_once( _BYJG_ADMIN_PATH . 'admin.byjg.html.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.user.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.group.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.phonebook.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.usergroups.php' );
require_once( _BYJG_ADMIN_PATH . 'byjg.prerequisite.php' );
require_once( _BYJG_ADMIN_PATH . '/provider/providerfactory.php' );

$task = JRequest::getVar( 'task', 'Default');
$act  = JRequest::getVar( 'act', 'Default');
$cid  = JRequest::getVar( 'cid', array(0));

$backend = new ByJGBackend( $act, $task, $cid );

if( $backend->CanHandle() )
{
	return $backend->Execute();
}

echo 'Cannot handle task: ' . $task . $act;
?>