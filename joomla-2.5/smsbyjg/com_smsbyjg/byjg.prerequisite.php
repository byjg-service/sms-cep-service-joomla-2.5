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

if( defined( 'BYJG_BACKEND_PREREQUISITE_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_PREREQUISITE_PHP', 1 );


class ByJGPrerequisite
{
	/**
	 * Execute all checks and return the result as array
	 */
	function Check()
	{
		$methods = get_class_methods( 'ByJGPrerequisite' );
		$result  = array();

		foreach( $methods as $m ){

			if( $m == 'Check' ){
				continue;
			}

			array_push($result, $this->$m() );
		}

		return $result;
	}

	/**
	 * Check if the fopen wrapper are available
	 * many provider need them
	 */
	function CheckFopenWrapper()
	{
		$key  = 'FopenWrapper';
		$val  = (bool)ini_get( 'allow_url_fopen' );
		$desc = 'php.ini - allow_url_fopen - byjg';

		return array( $key, $val, $desc );
	}

	/**
	 * Check php version > 4
	 */
	function CheckPhp5()
	{
		$key  = 'Php5';
		$val  = ((int)phpversion())<5?0:1;
		$desc = 'Php5 - phonebook import/export, nohnoh, mexado';

		return array( $key, $val, $desc );
	}

	/**
	 * Check if ssl stream wrapper
	 */
	function CheckSSLStreamWrapper()
	{
		$key  = 'SSLStreamWrapper';
		$val = 0;

		if( function_exists( 'stream_get_wrappers' ) ){
			$wrapper = 	stream_get_wrappers();

			if( array_search( 'https', $wrapper ) !== false ){
				$val = 1;
			}
		}

		$desc = 'nohnoh';

		return array( $key, $val, $desc );
	}

}//end class
?>