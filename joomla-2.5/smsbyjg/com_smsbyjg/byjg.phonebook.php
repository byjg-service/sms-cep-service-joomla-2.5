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

if( defined( 'BYJG_BACKEND_PHONEBOOK_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_PHONEBOOK_PHP', 1 );

/**
*  ByJG Phonebook class
*
* @package ByJG
* @subpackage Util
*/
class ByJGPhonebook
{
      var $_ownerID; //owner id from #__byjg_phonebook
	  var $_db; //refernce to the global database object

/**
s* The constructor creates a new user phonebook
*
**/
function ByJGPhonebook($id)
{
	$this->_db = &JFactory::getDBO();

    if( is_numeric( $id ) )
    {
        $this->_ownerID = $id;
    }
}
 
/**
* This function returns the user phonebook
*
**/
function getEntries( $offset = 0, $limit = 100, $search = null )
{
  //read joomla based user data
  $sql = "SELECT id, ownerid, number, name from #__byjg_phonebook WHERE ownerid=" . $this->_ownerID;

  if( !empty( $search ) )
  {
  	$sql .= " and number like '%" . $search . "%' or name like '%". $search . "%' ";  
  }
  
  $sql .= " limit $offset, $limit";
  
          
  $this->_db->setQuery($sql);

  if($this->_db->query() === false )
  {
     ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
     die;
  }

  return $this->_db->loadObjectList();
}

function getTotalEntryCount( $search = null )
{
	 //read joomla based user data
  $sql = "SELECT count(*) from #__byjg_phonebook WHERE ownerid=" . $this->_ownerID;
  
  if( !empty( $search ) )
  {
  	$sql .= " and number like '%" . $search . "%' or name like '%". $search . "%' ";
  }
  
  $this->_db->setQuery( $sql );

  if($this->_db->query() === false )
  {
     ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
     die;
  }

  return $this->_db->loadRow();
}

/**
* This function add's a new entry to the phonebook, return true if success otherwise false
*
* @param string name
* @param string number
**/
function addEntry( &$name, &$number )
{
  //check input
  if( strlen( $name ) <= 0 )
  {  	
    ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
    die;
  }

  $sql = "INSERT INTO #__byjg_phonebook VALUES(0, $this->_ownerID, '$number', '$name' )";
  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {  	
      ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
      die;
  }

  return true;
}


/**
* This function remove's a new entry from the phonebook, return true if success otherwise false
*
* @param int entryID
**/
function removeEntry($entryID)
{
  //check input
  if( !is_numeric($entryID) )
  {
       ByJGError::Alert( JText::_( 'BYJG_PHONEBOOK_REMOVE_ENTRY_FAILED' ) );
       die;
  }

  //create sql
  $sql = "DELETE FROM #__byjg_phonebook WHERE id=$entryID AND ownerid=$this->_ownerID LIMIT 1";
  $this->_db->setQuery($sql);

  if( $this->_db->query() === false )
  {
    ByJGError::Alert( JText::_( 'BYJG_SQLQUERY_ERROR' ) );
    die;
  }
  
  //remove from groups
  
  return true;
}

/**
* This function returns the complete user sms archive
*
**/
function getArchive( $offset, $limit )
{
 
	  //create sql to get all sended sms
    $sql = "SELECT * FROM #__byjg_sendsms WHERE userid=".$this->_ownerID . ' LIMIT ' . $offset .','.$limit;

    //setup query and check error
    $this->_db->setQuery($sql);

    if( $this->_db->query( ) == false )
    {
        ByJGError::Alert( JText::_( 'BYJG_SQLQUERY_ERROR' ) );
        die;
    }
    //load the sms and show it in html class
    $rows =  $this->_db->loadObjectList();

    //now try to replace number with names
     $sql ="SELECT name, number FROM #__byjg_phonebook WHERE ownerid=".$this->_ownerID;

    //setup query and check error
    $this->_db->setQuery($sql);

    if( $this->_db->query( ) == false )
    {
        ByJGError::Alert( JText::_( 'BYJG_SQLQUERY_ERROR' ) );
        die;
    }

    //load result object list
    $pb = $this->_db->loadObjectList();

    //replace numbers with names
    for($i=0; $i<= count($rows); $i++)
    {
      foreach($pb as $p )
      {
      	if( !isset( $row[$i] ) ){
      		continue;
      	}

        if( $rows[$i]->to == $p->number ){
          $rows[$i]->to = $p->name;
        }
      }
    }

    reset($rows);
    return $rows;  
}

/**
* This function returns the complete user sms archive
*
**/
function getArchiveTotalCount()
{
	$sql = "SELECT COUNT(*)  FROM #__byjg_sendsms WHERE userid=".$this->_ownerID;
	
	$this->_db->setQuery( $sql );

  	if($this->_db->query() === false )
  	{
    	 ByJGError::Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' )  );
     	die;
  	}

   return $this->_db->loadRow();	
}



}//end class
?>