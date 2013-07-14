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

if( defined( 'BYJG_PROVIDER_PROVIDER_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_PROVIDER_PROVIDER_PHP', 1 );


/**
*  Provider is the base class of all sms gateway provider
*
* @package ByJG
* @subpackage Provider
**/

class Provider
{
   	/**
   	*  The logical provider name
   	*  @var string
   	*/
    var $_name;
   	/**
   	*  All parameters needed by the provder like loginname, hostname ....
   	*  @var array
   	*/
    var $_params;
   	/**
   	*  The php filename in filesystem like byjgsms.php
   	*  @var string
	*/
    var $_file;
	/*
	*  crypto object
	*/
    var $_crypto;
   	/*
   	* db
   	*/
    var $_db;

	/**
   	*  Constructor, setting up name, file and parameters (empty array)
   	*/
      function Provider()
      {
        $this->_name 	= 'Base SMS Provider';
        $this->_file 	= basename( __FILE__ );
        $this->_params 	= array();
        $this->_crypto 	= new ByJGCrypt();
        $this->_db 		= &JFactory::getDBO();
      }
/**
*  This function is to register a provider in database
*
**/
      function register()
      {
       //check input
       if( $this->_name == 'Base SMS Provider' )
       {
           return;
       }
       //first check if our provider is already registerd
       $sql = "SELECT id from #__byjg_provider WHERE name='" . $this->_name ."'";

       $this->_db->setQuery($sql);

       if( $this->_db->query() == false )
       {
           echo "<script> alert('com_byjg --> registerProvider: database query failed !!!'); window.history.go(-1); </script>\n";
           return;
       }
        //get number of datasets
        $count = $this->_db->getAffectedRows();

        //if provider doesn't exists, register it
        if( $count == 0 )
        {
			$params = $this->_crypto->Encode( $this->_params );
            $sql ="Insert Into #__byjg_provider Values( 0, '$this->_name', '$this->_file', '$params', '0')";

            $this->_db->setQuery($sql);

            if( $this->_db->query() == false )
            {
                echo "<script> alert('com_byjg --> registerProvider: database query failed !!!'); window.history.go(-1); </script>\n";
              exit();
            }
         }
      }

   /**
   *  The sendSMS is for sending a sms, this function must be reimplemented in all dirved classes
   *  @param string $text
   *  @param string $from
   *  @param string $to
   /* @param string $errMsg
   */
      function sendSms( $text, $cod_ddd, $no_celular, $from, &$errMsg)
      {
      	return false; // return false, we are a dummy
      }
   /**
   *  The archiveSMS is for storing sms in database.
   *  @param string $text
   *  @param string $from
   *  @param string $to
   */
      function archiveSMS( $mensagem, $from, $to )
      {
          $sql = "SELECT id FROM #__byjg_provider WHERE active='1' LIMIT 1";

          $this->_db->setQuery($sql);

          if( $this->_db->query() === false )
          {
          	ByJGRedirect( 'index.php?option=com_byjg', JText::_( 'BYJG_SQLQUERY_ERROR' ) );
          }

          $row = $this->_db->loadObject();

          $user =& JFactory::getUser();
          $userId = $user->get('id');
          $sql = "INSERT INTO #__byjg_sendsms VALUES(0,   $userId ,  NOW(),  '$mensagem', '$from', '$to', $row->id )";

          //setup query and query
          $this->_db->setQuery($sql);

          if( $this->_db->query() === false )
          {
            ByJGRedirect( 'index.php?option=com_byjg', JText::_( 'BYJG_SQLQUERY_ERROR' ) );
          }

      }
   /**
   *  The loadConfigFromDB loads the config parameters form database.
   *  The config data is a serialzed string
   */

      function loadConfigFromDB()
      {
          //load config from db
           $sql = "SELECT params FROM #__byjg_provider WHERE name='$this->_name' LIMIT 1";

           $this->_db->setQuery($sql);

           if( $this->_db->query() === false )
           {
            ByJGRedirect( 'index.php?option=com_byjg', JText::_( 'BYJG_SQLQUERY_ERROR' ) );
            die();
           }

           $obj = $this->_db->loadObject();

           //provider does not exists in database
           if( is_null( $obj ) )
           {
           		return;
           }

           $p = $this->_crypto->Decode( $obj->params );

           if( is_array($p) ){ //if not something wrong
               $this->_params = $p;
           }
      }

      /**
       * buildQuery
       *
       * http_build_query emulation for old php 4 installations
       *
       * @param array $params
       * @return string $query url encoded query string
       */
      function buildQuery( $params )
      {

        if( !is_array( $params ) )
        {
        	return $params;
        }

        /*if( function_exists( 'http_build_query') )
        {
        	return http_build_query( $params );
        }*/ 

        foreach( $params as $key => $val )
        {
			$query .= urlencode( $key ) . '=' . urlencode( $val ) . '&';
        }

        //remove last &
        $query = substr( $query, 0, strlen($query)-1);

        return $query;
      }

} //end Provider class
?>