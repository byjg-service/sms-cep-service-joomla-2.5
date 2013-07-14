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
* SMS BYJG é um software livre. Esta versão pode ter sido modificado nos termos da 
* LGPL (Library ou Lesser General Public License), e como é distribuída inclui ou é derivado de 
* obras licenciado sob a Licença Pública Geral GNU ou outras licenças de software livre ou open source
*
* Este programa é distribuído na esperança que seja útil, mas SEM QUALQUER GARANTIA, 
* sem mesmo a garantia implícita de COMERCIALIZAÇÃO ou ADEQUAÇÃO PARA UM DETERMINADO PROPÓSITO.
*
**/

//check if joomla call us
//check if joomla call us
defined( '_JEXEC' ) or die( 'Restricted access' );

if( defined( 'BYJG_PROVIDER_BYJG_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_PROVIDER_BYJG_PHP', 1 );


/**
 * require our base class
 */
require_once('provider.php');


/**
*  ByJG is a sms gateway connection implementation (https://www.bijg.com.br)
*
* @package SMS BYJG
* @subpackage Provider
**/

class BYJG extends Provider
{

 /**
   *  Constructor, setting up name, file and parameters
   */
      function BYJG()
      {
        Provider::Provider();   //call base class constructor
        $this->_name = 'ByJG';
        $this->_file = basename( __FILE__ );

        $this->_params['username'] 	=  "pixx";			//default params
        $this->_params['password'] 	=  "px12345";		//default params
      }

   /**
   *  The sendSMS is for sending a sms, this function must be reimplemented in all dirved classes
   *  @param string $text
   *  @param string $from
   *  @param string $to
   * $to -> cod pais + ddd + celular ( Ex.: +55 - 85 - 99990000)
   */
      function sendSms( $text, $cod_ddd, $no_celular, $from, &$errMsg)
      {      	
        //get all params
		$usuario 	= $this->_params['username'];
		$senha 		= $this->_params['password'];
		
		// se a conta não iniciar com PIXX, não envia
		$permission = BYJG::checkoutUser($usuario);
		
		if ($permission == true) {
			$baseurl 	="http://www.byjg.com.br/site/webservice.php/ws/sms";
			$mensagem 	= urlencode($text);
			
			$ddd 		= $cod_ddd; // substr($to, 3, 2); // apenas dois digitos a partir do terceiro caracter da string que identificam o DDD da cidade
			$celular 	= $no_celular; // substr($to, -8); // somente os 8 ultimos digitos da string que identificam o celular destino
			$to 		= $ddd.$celular;
			$from 		= $from;
			
			// compose url
			$url 	= "$baseurl?httpmethod=enviarsms&";
			$url 	.= "ddd=$ddd&";
			$url 	.= "celular=$celular&";
			$url 	.= "mensagem=$mensagem&";
			$url 	.= "usuario=$usuario&";
			$url	.= "senha=$senha";
			
			$ret = file($url);
			
			// Trata o retorno. Gateway return like this { [0]=> string(14) "OK|0, Delivery" } 
			$sess = explode('|',$ret[0]);
	
			if ($sess[0] == 'OK') {
				$errMsg = $sess[0];
				$ok = true;
			} else {
				$errMsg = $sess[0].' - ' . $sess[1];
				$ok = false;
			}
		
		} else {
			$errMsg = 'A conta configurada neste servidor n&atilde;o segue o padr&atilde;o solicitado pelo componente (4 primeiros caracteres devem ser "pixx"';
			$ok = false;
		}
		
        return $ok;
		
      }//end sendSms


	  /*
	  * funcao que recupera informa�ao de saldo junto ao SMS Gateway - only SMS BYJG
	  */
	  function recoverBalance(&$msgBalance)
      {
		$usuario 	= $this->_params['username'];
		$senha 		= $this->_params['password'];
		$baseurl 	= "http://www.byjg.com.br/site/webservice.php/ws/sms";
		$url		= "$baseurl?httpmethod=creditos&usuario=$usuario&senha=$senha";

		$ret 		= fopen($url,"r");
		$ret 		= fgets($ret);
		$sess 		= explode('|',$ret);
		if ($sess[1] == 'ARRAY') {
			$creditos = explode(',',$sess[3]);
			echo "&nbsp;&nbsp;(Voc&ecirc;  tem <strong>".$creditos[0] . "</strong> cr&eacute;dito(s).";
			list($y, $m, $d) = explode('-', $creditos[1]);
			$mk=mktime(0, 0, 0, $m, $d, $y);
			$dob=strftime('%d/%m/%Y',$mk);
			echo  " Seus cr&eacute;ditos s&atilde;o v&aacute;lidos at&eacute; <strong>". $dob. "</strong>.)" . ' - Powered <a href="http://www.byjg.com.br/site/xmlnuke.php?module=byjg.login&amp;action=action.NEWUSER&amp;idrevenda=2290" target="_blank">www.ByJG.com.br</a>.';
		} else {
			$cod_erro = explode(',',$sess[1]);
			echo "&nbsp;&nbsp; (Erro - $cod_erro[0]) Aconteceu um erro ao tentar resgatar informacao de cr&eacute;dito junto ao provedor. Para criar sua conta visite: ".'<a href="http://www.byjg.com.br/site/xmlnuke.php?module=byjg.login&amp;action=action.NEWUSER&amp;idrevenda=2290" target="_blank">www.ByJG.com.br</a>.';
		}
	  }//end recoverBalance
	 
	 function checkoutUser( $pass)
     {
	 	$result = substr($pass, 0, 4);
	 	if ($result == 'pixx') 
		{
			$ok = true;
		} else {
			$ok = false;
		}
		
		return $ok;
	 }
	 
} //end class BYJG
?>