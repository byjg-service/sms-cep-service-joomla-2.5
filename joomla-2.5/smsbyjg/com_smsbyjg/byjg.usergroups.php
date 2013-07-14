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

if( defined( 'BYJG_BACKEND_USERGROUPS_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_USERGROUPS_PHP', 1 );

/**
*  ByJG User Group class, collection of single group classes
*
 * @package ByJG
 * @subpackage Util
**/
class ByJGUserGroups
{
     var $_groups;
     var $_ownerID;
     var $_db;

   /**
	* The constructor creates a new user group
	*
	**/
	function ByJGUserGroups($owernid)
	{
		$this->_db = &JFactory::getDBO();

       if( is_numeric($owernid) )
       {
            $this->_ownerID = $owernid;
            $this->init();
       }
	}

/**
* This function load's all user groups
*
**/
function init()
{

  $sql = "SELECT * FROM #__byjg_groups WHERE ownerid=".$this->_ownerID;

  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {
        ByJGError::Alert(   JText::_( 'BYJG_SQLQUERY_ERROR' )  );
        exit();
  }

  $lst = $this->_db->loadObjectList();

  foreach($lst as $l)
  {
    $g = new ByJGGroup();
    $g->init( $l->name );
    $this->_groups[] = $g;
  }

}

function reload()
{
  unset($this->_groups);
  $this->init();
}

function getEntries()
{
  return $this->_groups;
}

}  //end class
?>