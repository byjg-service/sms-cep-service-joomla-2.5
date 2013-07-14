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

if( defined( 'BYJG_FRONTEND_BYJG_HTML_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_FRONTEND_BYJG_HTML_PHP', 1 );


/**
*  ByJGFrontendHtml is the html frontend class for com_byjg
*
* @package ByJG
* @subpackage Frontend
**/
class ByJGFrontendHtml
{

/**
*  This function returns the java script code for the frontend
*  adlen +1 because \n
*
**/
  function JS()
  {
	?>
  <script type="text/javascript">
  <!--
  function SMSCharCount(adlen)
  {
    var maxlen = 160 - (adlen+1);

    var cc = $('sms_charcounter');
    var sb = $('sms_body');
    var len = sb.value.length;
    var text = sb.value;

    if( len <= maxlen )
    {
      var nLen = maxlen - len;
      cc.value = nLen;
    }else{
         alert('O limite de caracteres foi atingido.');
         sb.value = text.substring(0,maxlen);
         return;
    }
    
  }

  function checkInput(adlen){
     var maxlen = 160 - (adlen+1);

     var sender = document.getElementById('sms_send');

     if( sender.value.length <= 0 ){
       alert('Por favor informe o remetente.');
       return false;
     }

     var ddd = document.getElementById('sms_ddd');

     if( ddd.value.length <= 0 ){
       alert('Por favor informe um DDD');
	   ddd.focus();
       return false;
     }
	 
	 var recv = document.getElementById('sms_recv');

     if( recv.value.length <= 0 ){
       alert('Por favor indique um destinatario');
	   recv.focus();
       return false;
     }

     if( recv.value.length <= 7 ){
       alert('Numero de telefone deve conter 8 digitos');
	   recv.focus();
       return false;
     }
	 
     var body = document.getElementById('sms_body');
	 
	 if( body.value.length <= 0 ){
       alert('Por favor digite uma mensagem para ser enviada.');
	   body.focus();
       return false;
     }

     if( body.value.length > maxlen ){
       alert('Maximo de caracteres permitido foi atingido.');
	   body.focus();
       return false;
     }
	 
     return true;
  }

  function resetPhoneBook()
  {
    var phoneBook =  $('phonebookentry');
    
    if( phoneBook )
     {
        for( var i=0; i<phoneBook.length; i++)
        {
             var element = phoneBook[i];
             
             if( element )
             {
                 element.selected = 0;
             }
        }
    }
  }

  function addReceiver( number )
  {
    if( number )
    {
        var recv = $('sms_recv');
        recv.value += number;
        recv.value += ';';
    }
  }

  function resetReceiver(){
    var recv =  $('sms_recv');
    recv.value = '';
  }

  function updateRecv()
  {
 

   //reset group box and recv field
   resetReceiver();

   //get phonebook and recv field
   var phoneBook = $('phonebookentry');

   //check all items in phonebook if selected, if true add number to recv field
   for( var i=0; i<phoneBook.length; i++){
       var element = phoneBook[i];
       if( element ){
           if( element.selected ){
               addReceiver( element.value );
            }
       }
   }
  }
 
  function SelectGroup( groupId )
  {
    //first reset our phonebook
    
    $('filter').value = '';
    
    resetPhoneBook();
    resetReceiver();

    //Find the selected group object by groupid
    var group = null;
    for( var idx = 0; idx < ByJGPhoneBookGroups.length; idx++ )
    {
        var object = ByJGPhoneBookGroups[idx];

        if( object.groupId == groupId )
        {
			group = object;
			break;
        }
    }
  
    //something went wrong
    if( group == null )
    {
        return false;
    }

    //now create a array of option element with fitting group member data
    var len = 0;
    var optionArray = new Array();
    
    //for all group members
    for( var idx = 0; idx < group.members.length; idx++ )
    {
		var memberId = group.members[idx];

		//find a member in the whole phonebook
		for( var idx2 = 0; idx2 < ByJGPhoneBook.length; idx2++ )
		{

			var entry = ByJGPhoneBook[idx2];
			
			if( entry == null )
			{
				continue;
			}
					
			//match found, create html element and save it
			if( entry.id == memberId )
			{
				var option = document.createElement( 'option' );
				option.text  = entry.name + ' (' + entry.number + ')'  ;
				option.value = entry.number;	
				option.selected = 1;
		
				optionArray[len] = new Object();
				optionArray[len] = option;
			
				len++;					
			}
		}				       
    }

      
    //Remove all entries
	$('phonebookentry').length = 0;

	//rebuild option select
	for( var idx = 0; idx < optionArray.length; idx++ )
	{
		var el = optionArray[idx];
		$('phonebookentry').add( el, null );
	}

	updateRecv();

	return false;
	
  }//function

//-->
</script>

<?php
}  //end function JS

/**
* This function shows an error panel with variable messages
*
*  @param string msg
**/
 function showSMSSendErrorMessage( &$msg )
 {
  ?>
    <div class="componentheading">
            <?php echo JText::_( 'BYJG_ERROR' ); ?>:
    </div>

    <div id="notification">
		<p>
			<b>
				<?php echo $msg; ?>                    
			</b>
        </p>		
	</div>
  <?php
}  //end function    showSMSSendErrorMessage



/**
* This function shows the user config panel
*
*  @param mosParameters params
**/
function showConfigPanel( $smsUser, &$params )
{
    $mosParameters  = $params['mosParameters'];
	$option			= $params['option'];
	$Itemid			= $params['ItemId'];
	
		
	$cfg = new ByJGConfig();
	$policy = $cfg->Get('policy');
	
	if( strlen( $policy ) > 0 )
	{		
?>
	<br/><br/>
	<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
     	<?php echo JText::_( 'BYJG_POLICY' ) ;
     	?>:
    </div>
    
    <?php echo $policy; ?>
    <br/><br/>
<?php 		
	}
?>
	<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_( 'BYJG_USERNAME' );?>: <?php echo $smsUser->_name; ?> (<?php echo JText::_( 'BYJG_BALANCE' );?>: <?php echo $smsUser->balance(); ?>)
	</div>
	
	<div class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"></div>
	<!--  configuration form -->
	<div id="button_Wrap" style="overflow:hidden;padding:1px;">			
		<div id="btarchive" class="btns" style="float:left;width:100px;text-align:center;border:1px solid #ccc;padding:5px;background:#eee;">
			<form action="index.php" method="post">
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
				<input type="hidden" name="task" value="smsarchiv" />	
				<input style="" type="image" src="./media/com_byjg/images/archive.png" height="32px" width="32px" name="smsarchiv_button" alt="<?php echo JText::_( 'BYJG_SMSARCHIVE' );?>" title="<?php echo JText::_( 'BYJG_SMSARCHIVE' );?>" />
				<div style="clear:both"></div>
				<span style=""><?php echo JText::_( 'BYJG_SMSARCHIVE' );?></span>
			</form>
		</div>
	</div>
	<!-- DIV  id="button_Wrap" restante da DIV removida aqui -->
	
	<div style="clear:both"></div>
	<br />

<?php
}     //end function showConfigPanel


/**
* This function shows the send panel
*
*  @param mosParameters params
*  @param class user
*  @param string msg
**/
function showSendPanel( $smsUser , $params  )
{
	$mosParameters  = $params['mosParameters'];
	$option			= $params['option'];
	$Itemid			= $params['ItemId'];
	$msg			= isset( $params['msg'] )?$params['msg']:'';
	$tok 			= $params['token'];

	$c = $smsUser->_phoneBook->getTotalEntryCount();
    $phonebook 	= $smsUser->_phoneBook->getEntries( 0, $c[0]  );

    //build javascript object array    
?>

<script type="text/javascript">
<!--
var ByJGPhoneBook = new Array();

<?php

	$count = 0;
	foreach( $phonebook as $key => $val )
	{		
		echo "ByJGPhoneBook[$count] = new Object();";
		echo "ByJGPhoneBook[$count][\"id\"] = \"" . $val->id . "\";";
		echo "ByJGPhoneBook[$count][\"number\"] = \"" . $val->number . "\";";
		echo "ByJGPhoneBook[$count][\"name\"] = \"" . $val->name . "\";";
		echo "ByJGPhoneBook[$count][\"display\"] = \"" . $val->name . " ( " . $val->number . " ) \";";
		
		$count++;		
	}
	
	reset( $phonebook );
?>

function FilterPhoneBook()
{
	
	//Remove all entries
	$('phonebookentry').length = 0; 

	//get len and trim
	var val = $('filter').value.trim();
	var len = val.length;

	//add all entries
	if( val.length == 0 )
	{
		for( var idx = 0; idx < ByJGPhoneBook.length; ++idx )
		{
			var object = ByJGPhoneBook[idx];
			var option = document.createElement( 'option' );
			option.text  = object.name + ' (' + object.number + ')'  ;
			option.value = object.number;
			$('phonebookentry').add( option, null );
		}

		return;
	}

	var bEmpty = true;
	
	//apply the filter
	for( var idx = 0; idx < ByJGPhoneBook.length; ++idx )
	{
		var object = ByJGPhoneBook[idx];

		if( object.name.test( val, "i" ) )
		{
			var option = document.createElement( 'option' );
			
			option.text  = object.name + ' (' + object.number + ')'  ;
			option.value = object.number;

			$('phonebookentry').add( option, null );
			bEmpty = false;
			continue;
		}

		if( object.number.test( val, "i" ) )
		{
			var option = document.createElement( 'option' );
			
			option.text  = object.name + ' (' + object.number + ')'  ;
			option.value = object.number;

			$('phonebookentry').add( option, null );
			bEmpty = false;
			continue;
		}				
	}    

	if( bEmpty == true )
	{
	      var el = document.createElement( 'option' );
	      el.text  = 'No Matches';
	      el.value = '';
	      $('phonebookentry').add( el, null );
	}	
}

-->
</script>

<?php 
$groups = $smsUser->_groups->getEntries();
?>

<script type="text/javascript">
<!--
var ByJGPhoneBookGroups = new Array();

<?php

	$count = 0;
	foreach( $groups as $key => $val )
	{		
		echo "ByJGPhoneBookGroups[$count] = new Array();";
		echo "ByJGPhoneBookGroups[$count][\"groupId\"] = \"" . $val->_id . "\";" . "\r\n";
		echo "ByJGPhoneBookGroups[$count][\"groupName\"] = \"" . $val->_name . "\";" . "\r\n";		
		echo "ByJGPhoneBookGroups[$count][\"members\"] = new Array();";
		
		$idx = 0;
		foreach( $val->_members as $member )
		{
			echo "ByJGPhoneBookGroups[$count][\"members\"][$idx] = " . $member->id  . ";" . "\r\n";
			$idx++;
		}
				
		$count++;	
	}
	

	reset( $groups );
?>

-->
</script>


<?php     
    $config		= new ByJGConfig();
    $ad			= $config->Get( 'advertisment' );
    $adLen 		= strlen( $ad );
?>
	<div id="adminform">
		<form action="index.php" method="post" onsubmit="return checkInput();">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="sendSMS" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />
	
		<?php
		 //check if a msg is given, if show it
			if( strlen( $msg ) > 1 ){
				echo $msg;
			}
		?>

			<div id="megawrap" style="width: 90%; margin: 0 auto;">
				<div id="leftwrap" style="width:350px;float:left;overflow:hidden;margin-right:5px;">
					<span id="senderlbl"><?php echo JText::_( 'BYJG_SENDER' );?></span>
					<input readonly="readonly" readonly type="text" id="sms_send" name="sms_send" maxlength="8" size="10" value="<?php echo $smsUser->number(); ?>" style="border:1px solid #ccc" />			
					<div style="clear:both"></div>
					<br />
					
					<span id="dddlbl"><?php echo JText::_( 'BYJG_DDD' );?> (ex.: 85)</span>
					<input type="text" id="sms_ddd" name="sms_ddd" size="2" maxlength="2" value="85" style="border:1px solid #ccc" />
					<div style="clear:both"></div>
					<br />
					<span id="reciptlbl"><?php echo JText::_( 'BYJG_RECIPIENT' );?> (ex.: 88880000)</span>
					<input type="text" id="sms_recv" name="sms_recv" size="8" maxlength="8" style="border:1px solid #ccc" />
					<div style="clear:both"></div>
					<br />
					
					<div id="lblmsg" class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
						<span class="lbl_iput"><?php echo JText::_( 'BYJG_MESSAGE' );?> SMS: </span>
						<input onclick="SMSCharCount( <?php echo $adLen; ?> );"  value="<?php echo (160 - $adLen); ?>" size="4" type="text" name="sms_charcounter" id="sms_charcounter" readonly="readonly" style="float:right;border:none;"/>				
					</div>
					<div id="input_msg">
						<textarea onkeyup="SMSCharCount( <?php echo $adLen; ?> );"  onkeypress="SMSCharCount( <?php echo $adLen; ?> );" onkeydown="SMSCharCount( <?php echo $adLen; ?> );"  id="sms_body" name="sms_body" rows="10" cols="40" style="width: auto; height: auto; border:1px solid #ccc;" ></textarea>
					</div>
					<br />
					
					<?php if($adLen > 0){?>
						<span id="advertlbl"><?php echo JText::_( 'BYJG_ADVERTISMENT' );?>:</span>
						<textarea style="height: auto; width: 300px; border:1px solid #ccc;" name="ad" id="ad" readonly="readonly" rows="1" cols="35"><?php echo  $ad; ?></textarea>
					<?php } ?>
					
				</div>
				<!-- DIV  id="rightwrap" removida aqui -->
			</div>
			<div style="clear:both"></div>
			<input type="submit" name="send_button" value="<?php echo JText::_( 'BYJG_SEND'); ?>" style="margin-top:5px;float:left;"/>
		</form>		
	</div>
	<div style="clear:both"></div>
	<br />
	<div class="adminform" style="text-align:right;">
    	<span>
			<small>SMS BYJG desenvolvido sobre: <a href="http://mysms.joomlacoder.de" target="_blank">MySMS</a></small>
		</span>
	</div> 
<?php

	$this->JS();

}      //end function       showSendPanel


/**
*  This function is for showing the user config panel.
*
*  @param mosParameters params
*  @param array row
**/
function showUserConfigPanel($smsUser, $params)
{
	$mosParameters  = $params['mosParameters'];
	$option			= $params['option'];
	$Itemid			= $params['ItemId'];

    $this->JS();
?>
	<form action="index.php" method="post">
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		<input type="hidden" name="task" value="saveConfiguration" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />

		<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
		   <?php echo JText::_( 'BYJG_MY_CONFIG' ); ?>: <?php echo $smsUser->userName(); ?>
		</div>
		
		<br/>
		
		<div id="inputconfigwrap" style="width:270px;">
			<span style="float: left;"><?php echo JText::_( 'BYJG_PHONENUMBER' ); ?>:</span>
			<input type="text" name="myphonenumber" id="myphonenumber" size="20" value="<?php echo $smsUser->number();?>" style="border:1px solid #ccc; float: right;" />
			<div style="clear:both"></div>
			<br />
			
			<span style="float: left;"><?php echo JText::_( 'BYJG_COMMENT' );?>:</span>            
			<input type="text" name="mycomment" id="mycomment" size="20" value="<?php echo $smsUser->comment();?>" style="border:1px solid #ccc;  float: right;" />			
			<div style="clear:both"></div>
			<br />
			
			<input type="submit" name="change_button" value="<?php echo JText::_( 'BYJG_SAVE' ); ?>" />
			<input type="submit" name="cancel_button" value="<?php echo JText::_( 'BYJG_CANCEL' ); ?>" />			
		</div>       

	</form>

<?php

}  //end function showUserConfigPanel

/**
*  This function is for showing the sms archive
*
*
*  @param mosParameters params
*  @param array rows
**/
function showSendSMS($rows, $params, $user)
{
	$mosParameters  = $params['mosParameters'];
	$option			= $params['option'];
	$Itemid			= $params['ItemId'];
	$pageNav		= $params['pageNav'];
?>
     <form action="index.php" method="get">
       <input type="hidden" name="option" value="<?php echo $option; ?>" />
       <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
       <input type="hidden" name="task" value="smsarchiv" />

      <div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
           <?php echo JText::_( 'BYJG_SMSARCHIVE' );?>: <?php echo $user->userName() ?>
       </div>
	   <div id="button_Wrap" style="overflow:hidden;padding:1px;">			
	<div id="button_Wrap" style="overflow:hidden;padding:1px;">			
		<div id="btarchive" class="btns" style="float:left;width:100px;text-align:center;border:1px solid #ccc;padding:5px;background:#eee;">
			<form action="index.php" method="post">
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
				<input type="hidden" name="task" value="smsarchiv" />	
				<input style="" type="image" src="./media/com_byjg/images/archive.png" height="32px" width="32px" name="smsarchiv_button" alt="<?php echo JText::_( 'BYJG_SMSARCHIVE' );?>" title="<?php echo JText::_( 'BYJG_SMSARCHIVE' );?>" />
				<div style="clear:both"></div>
				<span style=""><?php echo JText::_( 'BYJG_SMSARCHIVE' );?></span>
			</form>
		</div>
	</div>
	</div>
    <br/>
    <table border="0" class="adminform" width="100%" cellpadding="1" cellspacing="0">
	  <tr>
	    <td width="25%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_DATE' ) ;?></b></td>
	    <td width="15%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_RECIPIENT' );?></b></td>
	    <td width="60%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_MESSAGE' );?></b></td>
	  </tr>

<?php
        foreach($rows as $sms )
        {
?>
        <tr>
           <td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
           <?php echo $sms->senddate; ?>
            </td>
            <td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
           <?php echo $sms->to; ?>
            </td>
            <td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
           <?php echo wordwrap( $sms->text, 55, '<br/>', true );  ?><br/><br/></td>

        </tr>
        <tr>

        <td colspan="3" style="border-bottom: 1px solid #cccccc;" >

        </td>
        </tr>

<?php
        }
?>
          <tr>        
            <td valign="top" align="center" colspan="3"  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
            <br /><?php echo $pageNav->getListFooter(); ?>
            </td>
          </tr>

          </table>

          </form>
<?php
}

/**
*  This function is for the phonebook config panel
*
*  @param mosParameters params
*  @param array rows
**/
function showPhonebook($rows, $params, $user)
{
	$mosParameters  = $params['mosParameters'];
	$option			= $params['option'];
	$Itemid			= $params['ItemId'];
	$tok			= $params['token'];
	$pageNav		= $params['pageNav'];
	$pharse			= $params['phrase'];
?>
                
	<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
           <?php echo JText::_( 'BYJG_MY_PHONEBOOK' ) ;?>: <?php echo $user->userName() ?>
    </div>                
                
	<!-- Phonebook Entries -->
	<div id="phbookEntries" style="overflow: hidden; padding:3px 0 10px;border-bottom:1px solid #ccc;">
	
		<form id="phoneBookForm" action="index.php" method="get">
			<input type="text" name="phrase" size="20" value="<?php echo $pharse;?>" style="border: 1px solid #ccc;margin-bottom:5px;" />
			<input type="submit" value="<?php echo JText::_( 'BYJG_SEARCH' ); ?>"/>	   
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="ItemId" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="phonebook" />

			<br/>
		   
			<table border="0" class="adminform" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="15%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><input style="margin-left: 0px;" type="checkbox" id="phoneBookListToogleButton" name="phoneBookListToogleButton" onClick="ToogleCheckBox()"/></td>
					<td width="25%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_NAME' ); ?></b></td>
					<td width="50%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_PHONENUMBER' ); ?></b></td>
				</tr>

				<?php
					foreach( $rows as $entry ) { ?>
						<tr>
							<td class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
								<input class="check-me" type="checkbox" name="phoneBookEntryList[]" value="<?php echo $entry->id; ?>"/>           
							</td>

							<td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
								<?php echo $entry->name; ?>
							</td>
			   
							<td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
								<?php echo $entry->number; ?>
							</td>           
						</tr>
						
						<tr>
							<td colspan="3" style="border-bottom: 1px solid #cccccc;" ></td>
						</tr>

				<?php } //for ?>
			</table>

			<div id="controlwrap" style="overflow: hidden;">
				<div id="cwLeft" style="float:left;">
					<input type="button" name="dPBB" id="dPBB" onClick="return DeletePhoneBookEntries();" value="<?php echo JText::_( 'BYJG_DELETE' );?>"/>
				</div>
				<div id="cwRight" style="float:right;">
					<?php echo $pageNav->getListFooter(); ?>
				</div>
			</div>
		</form>
	</div>

<script type="text/javascript">
function ToogleCheckBox() {
	$$( '.check-me' ).each( function( el ) { el.checked = !el.checked; } ); 
}

function DeletePhoneBookEntries() {
	 var el = $('phoneBookForm');
	 el.method='post';
	 el.elements['task'].value = 'DeletePhonebookEntries';        	 
	 el.submit();
	 return true;
}
</script>
   	   	
	<br/>
	
	<div id="phbooknewentries" style="overflow: hidden;padding:3px 0 10px;border-bottom:1px solid #ccc;">
		
		<form action="index.php" method="post">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="AddPhoneBookEntry" />       
			<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />

			<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
				<?php echo JText::_( 'BYJG_NEW_ENTRY' ); ?>:
			</div>
                
			<div id="phentrieswrapper">
				<span><?php echo JText::_( 'BYJG_NAME' );?>:</span>
				<div id="inputname">
				   <input type="text" name="contactname" style="border: 1px solid #ccc" />
				</div>
				<span><?php echo JText::_( 'BYJG_PHONENUMBER' ); ?>:</span>
				<div id="inputnumber">
					<input type="text" name="contactnumber" style="border: 1px solid #ccc" />
				</div>
			</div>

            <div id="phentriesbutton" style="margin-top:5px;">
				<input type="submit" value="<?php echo JText::_('BYJG_SAVE'); ?>"  />
            </div>
        </form>
	</div>
<br />

	<div id="importphbook" style="overflow: hidden;padding:3px 0 10px;border-bottom:1px solid #ccc;">
		<form action="index.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="importPhonebook" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />

			<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
				<?php echo JText::_( 'BYJG_IMPORT_PHONEBOOK' ); ?>:
			</div>
                 
			<div id="importwrap">
				<span>
					<?php echo JText::_( 'BYJG_NAME' );?>
				</span>
				<div id="inputimport">
					<input name="phonebook" type="file" />
				</div>
			</div>
			
			<div id="importbutton" style="margin-top:3px;">
				<input type="submit" name="save_button" value="<?php echo JText::_( 'BYJG_SAVE' )?>" />
            </div>
        </form>
	</div>

	<br />
	<div id="exportphbook" style="overflow: hidden;padding:3px 0 10px;border-bottom:1px solid #ccc;">
		<form action="index.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="exportPhonebook" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />

			<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
				<?php echo JText::_( 'BYJG_EXPORT_PHONEBOOK' ); ?>:
			</div>
            
			<div id="exportbutton">
				<input type="submit" name="save_button" value="<?php echo JText::_( 'BYJG_EXPORT' );?>" />
			</div>
		</form>
	</div>
<?php
} //end function phonebook


/**
*  This function is for user groups
*
*  @param mosParameters params
*  @param array rows
**/
function showUserGroups($phoneBookRows, $groupRows, $params, $user)
{
	  $mosParameters    = $params['mosParameters'];
	  $option			= $params['option'];
	  $Itemid			= $params['ItemId'];
	  $tok				= $params['token'];
?>
	<div id="grouplist" style="overflow: hidden;padding:3px 0 10px;border-bottom:1px solid #ccc;">
		<form action="index.php" method="post">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="task" value="deleteUserGroup" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />

			<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
				<?php echo JText::_( 'BYJG_USER_GROUP' ) ;?>: <?php echo $user->userName(); ?>
			</div>
			
			<br/>
			
			<table border="0" class="adminform" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="25%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_NAME' ); ?></b></td>
					<td width="60%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_MEMBERS' );?></b></td>
					<td width="15%" class="sectiontableheader<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>"><b><?php echo JText::_( 'BYJG_ACTION' );?></b></td>
				</tr>

				<?php
					$ids = '';
					if( count($groupRows) > 0 ){
						foreach($groupRows as $entry ){
					
							$ids .= $entry->_id .';';?>
					
							<tr>
								<td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
									<?php echo $entry->_name; ?>
								</td>
								<td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
									<?php
										foreach($entry->_members as $key=>$val){
											echo $val->name . ",";
										}?>
								</td>

								<td  class="sectiontableentry<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
									<input type="submit" name="delete_button_<?php echo $entry->_id; ?>" value="<?php echo JText::_( 'BYJG_DELETE' ); ?>" />
								</td>

							</tr>
						<?php }
					}?>			
			</table>
	   
			<input type="hidden" name="ids" value="<?php echo $ids; ?>" />
		</form>
	</div>
	<br/>

    <form action="index.php" method="post">
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		<input type="hidden" name="task" value="addUserGroup" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="prtoken" value="<?php echo $tok; ?>" />

		<div class="componentheading<?php echo $mosParameters->get( 'pageclass_sfx' ); ?>">
			<?php echo JText::_( 'BYJG_CREATE_NEW_GROUP' );?>:
		</div>
        
		<div id="groupnamewrap">
			<span><?php echo JText::_( 'BYJG_GROUPNAME' ); ?></span>
			<div id="inputgroupname">
				<input type="text" name="groupname" style="border:1px solid #ccc;"/>
			</div>
			
			<br />
			<span><?php echo JText::_( 'BYJG_MEMBERS' );?></span>
			<div id="groupmember" style="overflow: hidden;padding:3px 0 10px;">
				<?php
					//get min and max id for faster searching in dispatcher
					$maxID = 0;
					$minID = 0;

					foreach($phoneBookRows as $pbEntry){
						if( $maxID < $pbEntry->id ){
							$maxID = $pbEntry->id;
						}
						
						if( $minID > $pbEntry->id ){
							$minID = $pbEntry->id;
						}
				?>			

				<input type="checkbox" name="userid_<?php echo $pbEntry->id;?>" value="<?php echo$pbEntry->id;?>" />
            	<?php echo $pbEntry->name . '&nbsp;&nbsp;(' . $pbEntry->number . ' )'; ?>
            	<br/>

				<?php } ?>

				<input type="hidden" name="maxID" value="<?php echo $maxID;?>" />
				<input type="hidden" name="minID" value="<?php echo $minID;?>" />
			</div>
		</div>

		<div id="groupbutton">
			<input type="submit" name="save_button" value="<?php echo JText::_( 'BYJG_SAVE' ); ?>" />&nbsp;&nbsp;
			<input type="submit" name="back_button" value="<?php echo JText::_( 'BYJG_BACK' );?>" />
		</div>
	</form>

<?php
} //end function showUserGroups
}//end class
?>