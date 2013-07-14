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

if( defined( 'BYJG_USER_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_USER_PHP', 1 );

/**
*  ByJG User class
*
 * @package ByJG
 * @subpackage Util
**/
class ByJGUser {

      var $_byjgId; //user id from ByJG_joomlauser table
      var $_joomBlocked; //bool if user is blocked
      var $_byjgBlocked; //bool if user is blocked
      var $_credits; //user credit
      var $_joomId;  //joomla userid
      var $_valid; //true if user exists
      var $_name; //joomla name
      var $_userName; //joomla username
      var $_number; //phone number
      var $_comment; //byjg comment
      var $_phoneBook; //users phoenbook
      var $_groups; //all users groups
	  var $_db; //reference the global db object

/**
* The constructor creates a new byjg user object function loads all entries from joomla user table, and all entries from ByJG_joomlauser table and call html method
*
**/
function ByJGUser($id)
{

	$this->_db = &JFactory::getDBO();

	//	check input
    if( is_numeric( $id ) )
	{
     //setup default values
     $this->_joomId = $id;
     $this->_byjgId = -99;
     $this->_joomBlocked = true;
     $this->_byjgBlocked = true;
     $this->_valid = false;
     $this->_credits = -99;
     $this->_name = '';
     $this->_userName = '';
     $this->_comment = '';
     $this->_number = 0;
     $this->init();
     $this->_phoneBook = new ByJGPhonebook( $this->joomlaID() );
     $this->_groups = new ByJGUserGroups( $this->joomlaID() );
    }
}

/**
* This function reads the use values from database
*
**/
function init()
{
  //read joomla based user data
  $sql = "SELECT * from #__users WHERE id=" . $this->_joomId;

  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {
        ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
        die;
  }

  $user = $this->_db->loadObject();

  //check if user is blocked
  if( $user->block == 0 )
  { //user not blocked
      $this->_joomBlocked = false;
  }

  //setup name
  $this->_name 		= $user->name;
  $this->_userName 	= $user->username;

  //read byjg based user data
  $sql = "SELECT id, number, comment, state, credits from #__byjg_joomlauser WHERE userid=" . $this->_joomId;

  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {
        ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
        die;
  }

  $myUser = $this->_db->loadObject();

  //no entry found in table
  if( is_null( $myUser ) )
  {
		return;
  }

  if( $myUser->state == 1 )
  { //user is allowed to send sms
     $this->_byjgBlocked = false;
  }

  $this->_number = $myUser->number;
  $this->_byjgId = $myUser->id;
  $this->_credits = $myUser->credits;
  $this->_comment = $myUser->comment;
  $this->_valid = true;
}

/**
*  This function is a dummy, it calls int. It is only for better code reading
*
**/
function reload()
{
  $this->init();
}

/**
* This function returns true if user is blocked in joomla user administration or
* is not allowed to send sms ( ByJG user administration )
*
**/

function isBlocked()
{
  if( $this->_valid == false ){
    return true;
  }

  if( $this->_joomBlocked == true ){
    return true;
  }

  if( $this->_byjgBlocked == true ){
    return true;
  }

  return false;
}

/**
* This function returns the user credits
*
**/

function balance()
{
  return $this->_credits;
}

/**
* This function returns true if the new balance can be set otherwise false
*
**/

function setBalance($credits)
{
  //check input
  if( !is_numeric( $credits ) ){
    return false;
  }

 //check if user is blocked
  if( $this->isBlocked() == true ){
    return false;
  }

  $sql = "UPDATE #__byjg_joomlauser SET credits=".$credits." WHERE id=".$this->_byjgId."  AND userid=".$this->_joomId;

  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {
        ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
        die;
  }

  $this->_credits = $credits;

  return true;
}

/**
* This function returns the user phone number
*
**/

function number()
{
  return $this->_number;
}

/**
* This function returns the joomla userid
*
**/

function joomlaID()
{
  return $this->_joomId;
}

/**
* This function returns the byjg userid
*
**/

function ByJGID()
{
  return $this->_byjgId;
}

/**
* This function returns the username
*
**/

function userName()
{
  return $this->_userName;
}

/**
* This function returns the user comment
*
**/

function comment()
{
  return $this->_comment;
}

}//end class
?>