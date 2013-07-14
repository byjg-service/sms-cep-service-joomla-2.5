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

if( defined( 'BYJG_BACKEND_BACKEND_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_BACKEND_PHP', 1 );

/**
 * ByJG Backend dispatcher class
 *
 * @author Axel Sauerh�fer <axel@willcodejoomlaforfood.de>
 * @version 1.5.3
 * @package ByJG
 * @subpackage Backend
 */
class ByJGBackend
{
  /*
   * task to proccess
   * @var string
   */
  var $task;

  /*
   * task to proccess
   * @var string
   */
  var $action;

  /**
   * parameter array
   * @var array
   */
  var $cid;

  /**
   * global database object
   * @var object mosDatabase
   */
  var $db;

  /**
   * Error handler
   * @var object ByJGError
   */
  var $errorHandler;

  /**
   * html layer
   * @var object ByJGBackendHtml
   */
  var $html;

  /**
   * crypto class
   * @var object ByJGCrypt
   */
  var $crypto;

  /**
   * Constructor
   *
   * @param string $task task to execute
   * @param array  $params array filled wiht parameter and globals like $database, $ItemId, $option
   */
  function ByJGBackend( $act, $task = 'default',  $cid = array() )
  {
    $this->action		= $act;
    $this->task 		= $task;
    $this->html			= new ByJGBackendHtml();
    $this->errorHandler = new ByJGError();
    $this->crypto		= new ByJGCrypt();
    $this->cid			= $cid;
    $this->db			= &JFactory::getDBO();
  }

  /**
   * Execute
   *
   * The Execute method is the only method to call, it is the entry point.
   * It check's if the given task exists, and is callable, if not the default
   * task will be called by call_user_method
   */
  function Execute( )
  {
    $method = 'Do' . ucfirst( strtolower( $this->action ) ) . ucfirst( strtolower( $this->task ) );

    if( !method_exists( $this, $method ) ){
      return $this->DoDefault();
    }

    if( !is_callable( array( $this, $method ) ) ){
      return $this->DoDefault();
    }

   call_user_func( array( $this, $method ) );
  }

  /**
   * Check if we can handle the request
   *
   * This method checks if we can handle the given task.
   *
   * @return bool return true if the task is handable, otherwise false
   */
  function CanHandle()
  {
    $method = 'Do' . ucfirst( strtolower( $this->action ) ) . ucfirst( strtolower( $this->task ) );

    if( !method_exists( $this, $method ) ){
      return false;
    }

    if( !is_callable( array( $this, $method ) ) ){
      return false;
    }

    return true;
  }

  /**
   * Show Configuration Defalut
   *
   * Show the default panel for configuration
   */
  function DoConfigurationDefault()
  {
    $this->html->showCredits();
  }

  function DoDefaultDefault()
  {
    $this->html->showCredits();
  }

  /**
   * DoProviderDefault
   *
   * Show the default select provider panel
   */
	function DoProviderDefault()
  	{
       $dir = _BYJG_ADMIN_PATH . 'provider/';

       if( !file_exists( $dir ) )
       {
       		$this->errorHandler->Alert( JText::_( 'BYJG_PROVIDER_NOT_FOUND' ) );
       }
   
      $factory = new ProviderFactory();
      $factory->registerAllProvider();

       $sql = "SELECT * from #__byjg_provider WHERE name != 'Base SMS Provider'";

       $this->ExecuteSql( $sql );
       $rows = $this->db->loadObjectList();
       $this->html->showProviderSelectPanel( $rows );
  }

  /**
   * Do Edit a specific provider
   *
   * Show the edit panel for a provider
   */
  function DoProviderEdit()
  {
      //only one provider can be edited
      if( is_array( $this->cid ) ){
          $id = (int)$this->cid[0];
      }else{
          $id = (int)$this->cid;
      }

      //set query and execute it, load selected provider
      $sql = "SELECT * FROM #__byjg_provider WHERE id=$id";

	  $this->ExecuteSql( $sql );
      $row = $this->db->loadObject();

      $factory = new ProviderFactory();     
      $provider = $factory->getInstance( $row->name );
      

      if( $provider === false )
      {
      	$this->errorHandler->Alert( JText::_( 'BYJG_PROVIDERFACTORY_NOT_FOUND' ) );
      }

       $provider->_params = $this->crypto->Decode( $row->params );
       $this->html->editProvider( $row, $provider );
  }

  /**
   * DoProviderSave
   *
   * Save new settings for a provider like username, password etc.
   * Do encryption before store in database
   */
  function DoProviderSave()
  {
      $params = JRequest::getVar( 'providerparams', array() );
      $id 	  = JRequest::getVar( 'id', -1 );

      if( is_string( $params ) )
      {
          $params = explode(",", $params );
      }

      $array = array();

      foreach($params as $key)
      {
          if( !strlen( $key ) )
          {
            continue;
          }

          $tmp 		 = JRequest::getVar( $key );
          $array[$key] = $tmp;
      }

       $str = $this->crypto->Encode( $array );

      $sql = "UPDATE #__byjg_provider SET active='0'";
      $this->ExecuteSql( $sql );

      $sql = "UPDATE #__byjg_provider SET params='$str',active='1' WHERE id=$id";
      $this->ExecuteSql( $sql );

      ByJGRedirect( 'index.php?option=com_byjg&act=provider', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * DoProviderCancel
   *
   * User Canceld editing a provider, redirect to start page
   */
  function DoProviderCancel()
  {
    ByJGRedirect( 'index.php?option=com_byjg' );
  }

  /**
   * Default for user panel
   */
  function  DoUser()
  {
  	$this->DoUserDefault();
  }

  /**
   * DoUserDefault
   *
   * Show the user overview panle
   */
  function DoUserDefault()
  {
	  $mainframe = &JFactory::getApplication();
	  $option = JRequest::getCmd('option');

       $sql = "SELECT COUNT(*) AS COUNTER FROM #__users";
	   $this->ExecuteSql( $sql );

       $total 		= $this->db->loadResult();
       $limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mainframe->getCfg('list_limit') ) );
       $limitstart	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );

       jimport('joomla.html.pagination');
       $pageNav = new JPagination( $total, $limitstart, $limit );

       $sql = "SELECT id, name, username from #__users";
       $this->ExecuteSql( $sql );

       $rows = $this->db->loadObjectList();

       $sql = "SELECT id, userid, state, credits from #__byjg_joomlauser";
	   $this->ExecuteSql( $sql );
       $ByJGRows = $this->db->loadObjectList();
       $this->html->showUser( $rows, $ByJGRows, $pageNav );
  }

  /**
   * DoUserPublish
   *
   */
  function DoUserPublish()
  {
    if( !is_array( $this->cid ) ){
           $this->cid = array(  $this->cid );
      }

      foreach( $this->cid as $uid ){
     	 $sql = "SELECT COUNT(*) AS COUNT FROM #__byjg_joomlauser WHERE userid=" . $uid;
     	 $this->ExecuteSql( $sql );
     	 $row = $this->db->loadObject();

     	 if( $row->COUNT  > 0 )
     	 {
     		$sql = "UPDATE #__byjg_joomlauser SET state=1 WHERE userid IN( " . implode( ',', $this->cid ) . ")";
     	 }else{
     	 	$sql = "INSERT INTO #__byjg_joomlauser VALUES( 0, $uid, '','',1, 0 )";
     	 }

		$this->ExecuteSql( $sql );
      }

       ByJGRedirect( 'index.php?option=com_byjg&act=user', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * DoUserUnpublish
   *
   */
  function DoUserUnpublish()
  {
      if( !is_array( $this->cid ) )
      {
           $this->cid = array(  $this->cid );
      }

      $sql = "UPDATE #__byjg_joomlauser SET state=0 WHERE userid IN( " . implode( ',', $this->cid ) . ")";
	  $this->ExecuteSql( $sql );
      ByJGRedirect( 'index.php?option=com_byjg&act=user', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * Edit a user
   */
  function DoUserEdit()
  {
      if( !is_array( $this->cid ) ){
           $this->cid = array(  $this->cid );
      }

      $userid = $this->cid[0];

      if( !is_numeric( $userid[0] ) )
      {
        $this->errorHandler->Alert( JText::_( 'BYJG_EDIT_USER_INVALID_DATA' ) );
      }

        $sql = "SELECT * FROM #__users WHERE id=$userid LIMIT 1";
		$this->ExecuteSql( $sql );
        $row = $this->db->loadObject();

        $sql = "SELECT * FROM #__byjg_joomlauser WHERE userid=$userid LIMIT 1";
        $this->ExecuteSql( $sql );
        $ByJGRow = $this->db->loadObject();
        $this->html->editUser($row, $ByJGRow);
  }

  /**
   * Save user settings
   */
  function DoUserSave()
  {
      $number 	= JRequest::getVar( 'number' , ''  );
      $comment 	= JRequest::getVar( 'comment', '' );
      $credits 	= JRequest::getVar( 'credits', -99 );

      if( $credits == -99 || !is_numeric( $credits ) )
      {
        $this->errorHandler->Alert( JText::_( 'BYJG_INVALID_BALANCE' ) );
      }

      $id      = JRequest::getVar( 'id', -1 );

      if( $id == -1 )
      {
        $this->errorHandler->Alert( JText::_( 'BYJG_INVALUD_USER_DATA' ) );
      }

      $sql = "UPDATE #__byjg_joomlauser SET number='$number', comment='$comment', credits='$credits' WHERE userid=$id";
	  $this->ExecuteSql( $sql );

	  ByJGRedirect( 'index.php?option=com_byjg&act=user', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * Cancel user edit
   */
  function DoUserCancel()
  {
  	ByJGRedirect( 'index.php?option=com_byjg' );
  }

  /**
   * Show the load panel for multiple user
   */
  function DoUserLoadlistpanel()
  {
  	$this->html->showLoadPanel( $this->cid );
  }

  /**
   * Save the new credits for multipe user
   */
  function DoUserLoadlist()
  {
	$credits = JRequest::getVar( 'credits', -1 );
	$sql = "UPDATE #__byjg_joomlauser SET credits=$credits WHERE userid IN (" . implode(',',$this->cid) . ")";
	$this->ExecuteSql( $sql );
	ByJGRedirect( 'index.php?option=com_byjg&act=user', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * Wrapper to execute sql statments,
   * maybe put later logging etc.
   */
  function ExecuteSql( $sql )
  {
  	$this->db->setQuery($sql);

    if( $this->db->query() === false )
    {
        $this->errorHandler->Alert( JText::_( 'BYJG_SQLQUERY_ERROR' ) );
    }
  }

  /**
   * Do Advertisment default panel
   */
	function DoAdDefault()
	{
  		$sql = "SELECT value FROM #__byjg_config WHERE name='advertisment'";
  		$this->ExecuteSql( $sql );
 		$row = $this->db->loadObject();
 		$ad = is_null($row)?'':$row->value;
 		$this->html->showAdvertisment( $ad );
   }

  /**
   * Save advertisment
   *
   */
  function DoAdSave()
  {
      $ad = JRequest::getVar( 'ad',null);

      //check if a ad string exists in db
      $sql = "SELECT COUNT(*) AS CNT FROM #__byjg_config WHERE name='advertisment'";
	  $this->ExecuteSql( $sql );
      $row = $this->db->loadObject();

      if( $row->CNT == 0 )
      { // it is a insert
          $sql = "INSERT INTO #__byjg_config VALUES(0, 'advertisment', '$ad')";
      }else{  //update
          $sql = "UPDATE #__byjg_config SET value='$ad' WHERE name='advertisment' LIMIT 1";
      }

      $this->ExecuteSql( $sql );

      ByJGRedirect( 'index.php?option=com_byjg', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * Cancel edditing advertisment
   */
  function DoAdCancel()
  {
  	ByJGRedirect( 'index.php?option=com_byjg' );
  }

  /**
   * Default panel for global settings
   */
  function DoGlobalDefault()
  {
	//select all config parameter
	$sql = "SELECT id, name, value FROM #__byjg_config";
	$this->ExecuteSql( $sql );
	$config = $this->db->loadObjectList();

	$defaultConfig = array( 'maxsms' 		=> array( 'friendly' => 'Max. SMS:'    ,'type' => 'text'    , 'value' =>  0 ),
						    'mailonerror' 	=> array( 'friendly' => 'Enviar email quando houver erro?'    ,'type' => 'text'    , 'value' =>  0 ), 
							'email' 		=> array( 'friendly' => 'E-mail:'    ,'type' => 'text'    , 'value' =>  'youremail@localhost' ),
							'policy' 		=> array( 'friendly' => 'Alerta:'    ,'type' => 'textarea', 'value' =>  '' ) );

	foreach($config as $c )
	{
		if( isset( $defaultConfig[$c->name] ) )
		{
			$defaultConfig[$c->name]['value'] = $c->value;
		}
	}

	$this->html->showGlobal( $defaultConfig );
  }

  /**
   * Save global settings
   */
  function DoGlobalSave()
  {

	$config = JRequest::getVar( 'config', array() );

	foreach( $config as $key => $val ){

		$sql = "SELECT COUNT(*) AS C FROM #__byjg_config WHERE name='$key' ";
		$this->ExecuteSql( $sql );

		$result = null;
		$result = $this->db->loadObject();

		if( $result->C < 1 ){
			$sql = "INSERT INTO #__byjg_config VALUES( 0 , '$key', '$val')";
		}else{
			$sql = "UPDATE #__byjg_config SET value='$val' WHERE name='$key' LIMIT 1";
		}

		$this->ExecuteSql( $sql );
	}

	 ByJGRedirect( 'index.php?option=com_byjg&act=global', JText::_( 'BYJG_CHANGES_SAVED' ) );
  }

  /**
   * Show some information about com_byjg
   *
   */
  function DoAboutDefault()
  {
  	$this->html->showAbout();
  }

  /**
   * Cancel editting global settings
   */
  function DoGlobalCancel()
  {
	 ByJGRedirect( 'index.php?option=com_byjg' );
  }

  /**
   * Do perform a Prerequisites check
   */
  function DoPrereqDefault()
  {
		$checker = new ByJGPrerequisite();
		$data = $checker->Check();
		$this->html->showPrerequisite( $data );
  }

}//end class
?>
