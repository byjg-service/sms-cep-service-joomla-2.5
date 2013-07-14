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

if( defined( 'BYJG_BACKEND_CONFIG_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_CONFIG_PHP', 1 );

/**
 * Simple getter class for configuration values
 *
 * @package ByJG
 * @subpackage Util
 *
 */
class ByJGConfig
{
	/**
	 * Get a global configuration by name
	 *
	 * @param string $key
	 * @return string $value
	 */
	function Get( $key )
	{
		$database = &JFactory::getDBO();
		$sql = "SELECT * FROM #__byjg_config WHERE name='$key' LIMIT 1";
		$database->setQuery( $sql );
		$database->query();
		$obj = $database->loadObject();
		return is_null($obj)?false:$obj->value;
	}

}//end class
?>