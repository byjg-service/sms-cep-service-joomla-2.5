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

if( defined( 'BYJG_BACKEND_CRYPTO_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_CRYPTO_PHP', 1 );

/**
 * Crypto class for encode/decode provider settings.
 * Nobody want's his provider account data in cleartext in a database.
 *
 * @package ByJG
 * @subpackage Util
 */
class ByJGCrypt
{
	/**
	 * encoder array
	 */
	var $encoder;

	/**
	 * decoders array
	 */
	var $decoder;

	/**
	 * chipher key
	 */
	var $key;

	function ByJGCrypt()
	{
		$this->encoder = array();
		$this->decoder = array();

		//first of all is base64
		$this->encoder[] = 'EncodeBase64';
		$this->decoder[] = 'DecodeBase64';

		//the mcrypt pecl is available
		if( function_exists('mcrypt_encrypt') )
		{
			$this->encoder[] = 'EncodeMcrypt';
			$this->decoder[] = 'DecodeMcrypt';
		}

		$this->key 	= md5( substr_replace(JURI::root(), '', -1, 1) );
	}

	/**
	 * Encode
	 */
	function Encode( $obj )
	{
		//first serialze to get a string
		$string = serialize( $obj );

		for( $i = (count($this->encoder) - 1); $i>=0; --$i ){
			$encoder = $this->encoder[$i];
			$string = $this->$encoder( $string );
		}

		return $string;
	}

	/**
	 * Decode
	 */
	function Decode( $string )
	{
		foreach( $this->decoder as $decoder ){
			$string = $this->$decoder( $string );
		}

		return unserialize( $string );
	}

	/*
	 * Encode a give string with mcrypt
	 */
	function EncodeMcrypt( $string )
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$result = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_ECB, $iv);
		return $result;
	}

	/*
	 * Decode a give string with mcrypt
	 */
	function DecodeMcrypt( $string )
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$result = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_ECB, $iv);
		$result = rtrim($result, "\0");
		return $result;
	}

	/*
	 * Encode a give string with base64
	 */
	function EncodeBase64( $string )
	{
		return base64_encode( $string );
	}

	/*
	 * Decode a give string with base64
	 */
	function DecodeBase64( $string )
	{
		return base64_decode( $string );
	}
}
?>