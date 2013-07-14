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

if( defined( 'BYJG_FRONTEND_BYJG_CONTROLLER_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_FRONTEND_BYJG_CONTROLLER_PHP', 1 );

/**
 *
 * Frontend Dispatcher for com_byjg
 *
 *
* @package ByJG
* @subpackage Frontend
 */
class ByJGFrontend
{
	/*
	 * task to proccess
	 * @var string
	 */
	var $task;

	/**
	 * parameter array
	 * @var array
	 */
	var $params;

	/**
	 * sms user object
	 * @var object smsUser
	 */
	var $user;

	/**
	 * html layer
	 * @var object ByJGFrontendHtml
	 */
	var $html;

	/**
	 * global database object
	 * @var object mosDatabase
	 */
	var $db;

	/**
	 * global mainframe object
	 * @var object mosMainframe
	 */
	var $mainframe;

	/**
	 * Error handler
	 * @var object ByJGError
	 */
	var $errorHandler;

	/**
	 * Constructor
	 *
	 * @param string $task task to execute
	 * @param array  $params array filled wiht parameter and globals like $database, $ItemId, $option
	 */
	function ByJGFrontend( $task = 'default', $params = array( ) )
	{
		$this->task 		= $task;
		$this->params 		= $params;
		$this->html			= new ByJGFrontendHtml();
		$this->errorHandler = new ByJGError();

		if( isset( $params['ByJGUser'] ) )
		{
			$this->user = $params['ByJGUser'];
		}

		if( isset( $params['mosDatabase'] ) )
		{
			$this->db = $params['mosDatabase'];
		}

		if( isset( $params['mosMainframe'] ) )
		{
			$this->mainframe = $params['mosMainframe'];
		}

		$tok = -99;

		if( isset( $_REQUEST['prtoken'] ) )
		{
			$tok = (string) $_REQUEST['prtoken'];
		}

		if( $tok != -99 )
		{
			if( $this->IsPostReload( $tok ) )
			{
				$this->errorHandler->Alert( JText::_( 'BYJG_INVALID_POSTRELOAD_TOKEN' ) );
			}
		}

		$this->params['token'] = $this->CreateToken();
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
		$method = 'Do' . ucfirst( strtolower( $this->task ) );

		if( !method_exists( $this, $method ) )
		{
			return $this->DoDefault();
		}

		if( !is_callable( array( $this, $method ) ) )
		{
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
		$method = 'Do' . ucfirst( strtolower( $this->task ) );

		if( !method_exists( $this, $method ) )
		{
			return false;
		}

		if( !is_callable( array( $this, $method ) ) )
		{
			return false;
		}

		return true;
	}

	/**
	 * Default
	 *
	 * The Default method, is called when no taks is given oder some unknwon.
	 * This method show the send panel. It also reload's the user data.
	 */
	function DoDefault()
	{
		$this->user->reload();
		$this->html->showConfigPanel( $this->user, $this->params );
  		$this->html->showSendPanel( $this->user,  $this->params  );
  		
	}

	/**
	 * Show configuration panel
	 *
	 * Show the user configuartion panel, here the user can setup his phonenumber and a comment.
	 * The comment field is optional
	 */
	function DoConfiguration()
	{
		//we are comming from the default task, redirect with get, strip params
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
		{							
			ByJGRedirect( $this->CreateRedirectUrl( 'configuration' ) );			
		}	
		
		$this->html->showUserConfigPanel( $this->user, $this->params );
	}

	/**
	 * Save new configuration
	 *
	 * Do save the new user configuration if submit button is pressed, otherwise cancel the action and redirect
	 * to the default view.
	 *
	 * @todo make the phonenumber length configable
	 */
	function DoSaveConfiguration()
	{
		//check if user press the cancel button
  		if( isset( $_POST['cancel_button'] ) )
  		{
  			ByJGRedirect( $this->CreateRedirectUrl() );
  		}

  		$number 	=  JRequest::getVar( 'myphonenumber' );
  		$comment 	=  JRequest::getVar( 'mycomment' );

  		//filter user input
  		$this->Filter($number);
  		$this->Filter($comment);

  		if( !is_string( $number) )
  		{
  			$this->errorHandler->Alert( JText::_( 'BYJG_INVALID_PHONENUMBER' ) );
  		}

  		// a normal german mobile phone number has the length 11
  		if( strlen( $number ) < 9  )
  		{
  			$this->errorHandler->Alert( JText::_( 'BYJG_INVALID_PHONENUMBER' ) );
  		}

		//create sql and query
  		$sql = "UPDATE /*com_byjg->dosaveconfiguration*/ #__byjg_joomlauser SET number='$number', comment='$comment' WHERE userid=". $this->user->joomlaID() ." AND id=". $this->user->ByJGID();
  		$this->db->setQuery( $sql );

	  	if( $this->db->query( $sql ) === false )
	  	{
	  		$this->errorHandler->Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' ) , $this->db->getErrorMsg() );
  	  	}

  	  	ByJGRedirect( $this->CreateRedirectUrl() , JText::_( 'BYJG_CHANGES_SAVED' ) );
	}

	/**
	 * Show users smsarchiv
	 *
	 * Show all sended sms.
	 */
	function DoSmsarchiv()
	{
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
		{							
			ByJGRedirect( $this->CreateRedirectUrl( $this->task ) );			
		}	
		
		$mainframe = $this->params['mosMainframe'];				
		
		$limit    	= $mainframe->getUserStateFromRequest( "byjg.frontend.smsarchiv.limit", "limit", 25, "int" );
		$offset   	= JRequest::getVar( 'limitstart', 0, '', 'int' );
		
		$total = $this->user->_phoneBook->getArchiveTotalCount();		 		
		$pageNav = new JPagination( $total[0], $offset, $limit );
		
		$params = $this->params;
		$params['pageNav'] = $pageNav;
		
		$rows = $this->user->_phoneBook->getArchive( $offset, $limit  );		
 		$this->html->showSendSMS( $rows, $params, $this->user );
	}

	/**
	 * Show users phonebook
	 */
	function DoPhonebook()
	{		
		//we are comming from the default task, redirect with get, strip params
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
		{							
			ByJGRedirect( $this->CreateRedirectUrl( $this->task ) );			
		}	
		
		$pharse = JRequest::getVar( 'phrase', '' );	
		$this->Filter( $pharse );
		
		$mainframe = $this->params['mosMainframe'];
				
		$limit    	= $mainframe->getUserStateFromRequest( "byjg.frontend.phonebook.limit", "limit", 25, "int" );
		
		if( $limit == 0 )
		{
			$limit = $this->user->_phoneBook->getTotalEntryCount();
			$limit = $limit[0];
		}
		
		$offset   	= JRequest::getVar( 'limitstart', 0, '', 'int' );
		
		$params = array();
		$params = $this->params;
		$params['phrase'] = $pharse;
				
		$rows 		= $this->user->_phoneBook->getEntries( $offset, $limit, $pharse ); 	
		$total 		= $this->user->_phoneBook->getTotalEntryCount( $pharse );

		$pageNav = new JPagination( $total[0], $offset, $limit );			
		$params['pageNav'] = $pageNav;
		 			
 		$this->html->showPhonebook( $rows, $params, $this->user );
	}
	
	/**
	 * Add a new phonebook entry
	 */
	function DoAddphonebookentry()
	{
		$url = 'index.php?';
		$url .= 'option=' . $this->params['option'];
		$url .= '&'; 
		$url .= 'ItemId=' . $this->params['ItemId'];
		$url .= '&'; 
		$url .= 'task=phoneBook';
						
 		//get name and number from form
 		$name 	= JRequest::getVar( 'contactname', '' );
 		$number = JRequest::getVar( 'contactnumber', '' );

  		$this->Filter( $name );
  		$this->Filter( $number );

  		if( $this->user->_phoneBook->addEntry( $name, $number ) == false )
  		{
  			$this->errorHandler->Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' ) , $this->db->getErrorMsg() );
  		}

  		ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ), JText::_( 'BYJG_PHONEBOOKENTRY_ENTRY_SUCCESSFULLY_ADDED' ) );
	}

	/**
	 * Delete a phonebook entry
	 *
	 * Delete a entry from users phonebook
	 */
	function DoDeletephonebookentries( )
	{
		$option = JRequest::getVar( 'option', 'byjg' );
		$ItemId = JRequest::getVar( 'ItemId', 0 );
				
		 //get a complete comma seperated list form id's ( database )
  		$ids = JRequest::getVar( 'phoneBookEntryList', array() );  	

  		if( empty( $ids ) )
  		{
  			ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ) );
  		}
  		
  		 //something wrong
  		if( $id == -1 )
  		{
    		$this->errorHandler->Alert( JText::_( 'BYJG_INTERNAL_ERROR' )  );
    		die;
  		}

  		foreach( $ids as $k => $id )
  		{
  			
  			foreach( $this->user->_groups->GetEntries() as $k => $v )
  			{
  				$v->deleteMember( $id );  				
  			}  			
  			
  			if( $this->user->_phoneBook->removeEntry( $id ) == false )
  			{
      			$this->errorHandler->Alert( JText::_( 'BYJG_DEL_PHONEBOOK_ENTRY_FAILED' ) );
  			}	
  		
  		}
  		  	
  		ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ) , JText::_( 'BYJG_DEL_PHONEBOOK_ENTRY_SUCCESSFULLY' ) );
	}

	/**
	 * Show user groups
	 *
	 * Show the configured uers groups
	 */
	function DoUserGroup()
	{
			//we are comming from the default task, redirect with get, strip params
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
		{							
			ByJGRedirect( $this->CreateRedirectUrl( $this->task ) );			
		}
		
  		$pbRows = $this->user->_phoneBook->getEntries();
    	$grRows = $this->user->_groups->getEntries();
    	$this->html->showUserGroups($pbRows, $grRows, $this->params, $this->user );
	}

	/**
	 * Delte a user group
	 *
	 */
	function DoDeleteusergroup()
	{
		  //get a complete comma seperated list form id's ( database )
  		$ids = JRequest::getVar( 'ids', '');
  		$this->Filter( $ids );
  		$ids_a = explode( ';', $ids ); //create a array of ids

	  $id = -1;
	  //check what delete button is clicked
  		foreach($ids_a as $entryid)
  		{
    		$button = "delete_button_$entryid";

    		if( isset( $_REQUEST[$button] ) ){  //we found the correct group
       			$id = $entryid;
       			break;
    		}
  		}

  		if( $id == -1 ){     //something wrong
    		$this->errorHandler->Alert( JText::_( 'BYJG_INTERNAL_ERROR' ) );
  		}

		  //load group by id and than delete it
  		$g = new ByJGGroup();
  		$g->init($id);
  		$g->delete();

		  //reload sms user  groups
  		$this->user->_groups->reload();

  		ByJGRedirect( $this->CreateRedirectUrl( 'usergroup' ) );
	}

	/**
	 * Add a new user group
	 */
	function DoAddusergroup()
	{
		 //check if user canceld action, if true return
 		if( isset($_REQUEST['back_button'] ) )
 		{
 			ByJGRedirect( $this->CreateRedirectUrl() );
 		}

		  //now get min and max id set defaults to -99
  		$minID = JRequest::getVar( 'minID', -99);
  		$maxID = JRequest::getVar( 'maxID', -99);

  		//something strange, abrot here
  		if( $minID == -99 || $maxID == -99 )
  		{
    		$this->errorHandler->Alert( JText::_( 'BYJG_ADD_GROUP_FAILED' ) );
  		}

	   //in request are checkboxes, the values are the userids ( userid=id from phonebook entry )
	   //now collect userids from request
   		$userIDS = array();

   		for($i=$minID; $i<=$maxID; $i++)
   		{
     		$id = 'userid_'.$i;

     		if( isset( $_POST[$id] ) )
     		{
       			$t = JRequest::getVar( $id, -99 );

       			if( $id == -99 )
       			{
          			$this->errorHandler->Alert( JText::_( 'BYJG_ADD_GROUP_FAILED' ) );
		       	}else{
        			$userIDS[]=$t;
       			}
     		}
		}

	   //nothing selected
   		if( count($userIDS) == 0 )
   		{
   			$this->errorHandler->Alert( JText::_( 'BYJG_ADD_GROUP_NO_SELECTION' ) );
   		}

   		$groupName =  JRequest::getVar( 'groupname', '');

		if( strlen($groupName) <= 1 )
		{
      		$this->errorHandler->Alert( JText::_( 'BYJG_GROUPNAME_MISSING' ) );
   		}

   		$this->Filter( $groupName );

	  //create a new user group, if group exists it will be loaded otherwise it will be created
	  //if init is called with a non numeric value
  	  $group = new ByJGGroup();
  	  $group->init($groupName);

	  //now add every entry
  		foreach($userIDS as $userid)
  		{
    		if( $group->addMember( $userid ) === false )
    		{
      			$this->errorHandler->Alert( JText::_( 'BYJG_GROUP_ADD_MEMBER_FAILED' ) );
    		}
  		}


    //reload sms user  groups
  	$this->user->_groups->reload();
  	ByJGRedirect( $this->CreateRedirectUrl( 'usergroup' ) );
  	
	}

	/**
	 * Import to users phonebook by a csv file
	 */
	function DoImportphonebook ()
	{					
		
		if( !isset( $_FILES['phonebook'] ) )
		{
			ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ) , JText::_( 'BYJG_PHONEBOOKIMPORT_FAILED' ) );
		}

		$data = $_FILES['phonebook'];

		//try to open tmp file
		$handle = fopen( $data['tmp_name'], 'r' );
		
		

		if( !$handle )
		{
			ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ) , JText::_( 'BYJG_PHONEBOOKIMPORT_FAILED' ) );
		}
		
		while( ( $data = fgetcsv ( $handle, 1000, ";") ) !== false ){
			list( $name, $number ) = $data;
				if( $this->user->_phoneBook->addEntry( $name, $number ) == false ){
					$this->errorHandler->Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' ) , $this->db->getErrorMsg() );
  				}
		}

		ByJGRedirect( $this->CreateRedirectUrl( 'phoneBook' ) , JText::_( 'BYJG_PHONEBOOKIMPORT_SUCCESSFULLY' ) );
	}

	/**
	 * Export users phonebook in csv format
	 *
	 * @todo check php if > 5.1, it not do some error message stuff
	 */
	function DoExportphonebook()
	{
		//check here php version > 5.1
		$entries = $this->user->_phoneBook->getEntries();
		$handle = fopen( 'php://temp', 'r+' );

		foreach( $entries as $entry ){
			fputcsv( $handle , array( $entry->name, $entry->number ), ';' );
		}

		rewind($handle);
		$content = stream_get_contents($handle);
		fclose($handle);

		header('Content-type: text/comma-separated-values');
		header('Content-Disposition: attachment; filename="phonebook.csv"');
		echo $content;
		exit();
	}

	/**
	 * Send a sms
	 *
	 * The most important method form the component. Send a sms.
	 *
	 */
	function DoSendSms( )
	{
	    //check global maxsms parameter before sending
		$sql = " /*com_byjg:dosendsms select global sms litmit */
				SELECT value FROM #__byjg_config WHERE name='maxsms' LIMIT 1";

		$this->db->setQuery( $sql );

		//check result and output a message
  		if( $this->db->query( $sql ) == false )
  		{
    		$this->errorHandler->Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' ) , $this->db->getErrorMsg() );
  		}

  		$maxsms = $this->db->loadResult();

  		if( !is_null( $maxsms ) && $maxsms > 0 )
  		{
  			$limit = $maxsms;

		  	$sql = "SELECT COUNT(*) AS COUNTER FROM #__byjg_sendsms";
  			$this->db->setQuery($sql);

  			//check result and output a message
  			if( $this->db->query($sql) == false )
  			{
    			$this->errorHandler->Alert(  JText::_( 'BYJG_SQLQUERY_ERROR' ) , $this->db->getErrorMsg() );
  			}

  	 		$row = $this->db->loadObject();

  	 		if( $row->COUNTER >= $limit  )
  	 		{
  	 			$this->errorHandler->Alert( JText::_( 'BYJG_GLOBAL_LIMIT_REACHED' ) );
  	 		}
  		}

  		$msg = '';

		//get input parameters
  		$sms_body = JRequest::getVar('sms_body', ''); //dont check body, if we want to send a empty sms it is ok
  		$sms_send = JRequest::getVar('sms_send', '');
			$from = $sms_send;
  		$sms_ddd  = JRequest::getVar('sms_ddd', '');
		$ad       = JRequest::getVar('ad', '');
  		
  		if( strlen( $sms_body ) == 0 )
  		{ 
  			$this->errorHandler->Alert( JText::_( 'BYJG_SMS_BODY_EMPTY' ) );
  		}
  		
		/*
		 *  check sms sender
		 	
  		if( strlen( $sms_send ) < 8 )
  		{
    		$this->errorHandler->Alert( JText::_( 'BYJG_INVALID_SENDER' ) );
  		}
  		
  		*/

  		$sms_recv = JRequest::getVar( 'sms_recv', '' );
  		$this->Filter( $sms_recv );

	   //check sms recv
  		if( strlen( $sms_recv ) < 8  )
  		{
      		$this->errorHandler->Alert( JText::_( 'BYJG_INVALID_RECIPIENT') );
      		die;
  		}

	  //append advertisment to sms
  		if( strlen( $ad ) > 0 )
  		{

	  		$bodyLen = strlen( $sms_body);
	  		$adLen   = strlen( $ad );
	  		$len = $bodyLen + $adLen;

	  		if( $len > 160 )
	  		{
				$sms_body = substr( $sms_body, 0, 159 - strlen( $ad ) );
				$sms_body .= "\n" . $ad;
	  		}else{
      			$sms_body .= "\n" . $ad;
	  		}
  		}

		  //now check if the sms body is not longer that 160
 		if( strlen( $sms_body ) > 160 )
 		{
  			$sms_body = substr( $sms_body, 0, 160 );
 		}

		  //get the current sms provider
		  //create provider factory
  			$factory = new ProviderFactory();
  			$provider = $factory->getActiveInstance();

		  //check provider here
  			if( $provider === false )
  			{
       			$this->errorHandler->Alert( JText::_( 'BYJG_NO_ACTIVE_PROVIDER' ) );
       			die;
  			}

  			$errMsg = '';
  			$arr = explode( ";", $sms_recv );

  		foreach( $arr as $recv )
  		{
       		$recv = trim($recv);  //trim whitespaces

	       if( strlen( $recv ) < 8 )
	       {
    	        continue;
       	   }

		       //check user balance
       		if( $this->user->balance() <= 0  )
       		{
				$this->errorHandler->Alert( JText::_( 'BYJG_NOT_SUFFICIENT_FUNDS' ) );
       		}


       		{ //trigger plugins if available
       			$dispatcher = JDispatcher::getInstance();
       			JPluginHelper::importPlugin( 'byjg' );

       			$data = array( 'message'  => $sms_body,
       						   'sender'	  => $sms_send,
       						   'receiver' => $recv );

       			$result = $dispatcher->trigger( 'onSendSms', $data );


       			$abort = false;

       			foreach( $result as $code )
       			{
       				if( $code == false )
       				{
       					$abort = true;
       					break;
       				}
       			}

       			//a plugin has returned false, so we cancel the send process
       			if( $abort == true )
       			{
       				ByJGRedirect( $this->CreateRedirectUrl() ,  JText::_( 'BYJG_PLUGIN_ABORT' ) );
       			}
       		}
       		
			/*
       		* Chamada da funcao alterada por BYJG para atender ao Gateway ByJG
			*         alterado em 09.10.10
			* $ret = $provider->sendSMS( $sms_body, $sms_send, $recv, $msg );
			*/
			$ret = $provider->sendSMS( $sms_body, $sms_ddd, $recv, $sms_send, $msg );
       		
	       //sms sending failed
    	   if( $ret == false )
    	   {
    	   		$errMsg .= JText::sprintf( 'BYJG_SEND_SMS_FAILED', $recv);
				$errMsg = $errMsg  . ' (' . $msg . ').';
       		}else{

       		   //dont forget to update the user balance
            	$bal = (int) $this->user->balance();
            	$bal--;

            	if( $this->user->setBalance( $bal ) == false )
            	{
                	$this->errorHandler->Alert( JText::_( 'BYJG_CHANGE_BALANCE_FAILED' ) );
            	}
       			
	           //sendind was ok, so archive it
			   $to	 = $sms_ddd . $recv;
			   $text = $sms_body;
    	       $provider->archiveSMS($text, $from, $to);   	          	               	    

            	$errMsg .= JText::sprintf( 'BYJG_SEND_SMS_SUCCESSFULLY',  $recv);
				$errMsg = $errMsg  . ' (' . $msg . ').';
       		}
  		}

  		ByJGRedirect( $this->CreateRedirectUrl(),  $errMsg );
	}

	/**
	 * Filter a given string by referance
	 *
	 * @param string &$param to filter
	 */
	function Filter( &$param )
	{
		 $param = htmlentities( strip_tags( $param ) , ENT_QUOTES );
	}

	/**
	 * Check if we alreay know the given token
	 *
	 * @param string $token
	 * @return bool true if it is a post reload otherwise false
	 */
	function IsPostReload( $token )
	{		
		if( $_SERVER['REQUEST_METHOD'] != 'POST' )
		{
			return false;
		}
		
		//check if our session array exists, if not create it
		if( !isset( $_SESSION['prArray'] ) || !is_array( $_SESSION['prArray'] ) ){
    		$_SESSION['prArray'] = array();
		}

		//check if given token is already known, if true this is a post reload, otherwise post is ok
		if( isset( $_SESSION['prArray'][$token] ) ){
  			return true;
		}else if( isset( $_SESSION['prArrayCreated'][$token] ) ){
  			$_SESSION['prArray'][$token] = $token;
  			return false;
		}else{
			  //the token is not from com_byjg !!! maybe a attack
			$this->errorHandler->Alert( JText::_( 'BYJG_POST_RELOAD_BLOCK' ) );
		}

		return false;
	}
	
	
	function CreateRedirectUrl( $task = '' )
	{
		$url = 'index.php?';
		$url .= 'option=' . $this->params['option'];
		$url .= '&'; 
		$url .= 'Itemid=' . $this->params['ItemId'];
		
		if( strlen( $task ) > 0 )
		{
			$url .= '&'; 
			$url .= 'task=' . $task;			
		}
		
		return $url;
	}

	/**
	 * Create a new token
	 */
	function CreateToken()
	{
  		if( !isset($_SESSION['prArrayCreated'] ) || !is_array($_SESSION['prArrayCreated']) ){
    		$_SESSION['prArrayCreated'] = array();
  		}

  		$tok = md5( uniqid( rand() ) );

		//save all created post token, a incomming post token must set in this array (prArrayCreated) and not set in prArray
  		$_SESSION['prArrayCreated'][$tok]=$tok;

  		return $tok;
	}
}//end class
?>