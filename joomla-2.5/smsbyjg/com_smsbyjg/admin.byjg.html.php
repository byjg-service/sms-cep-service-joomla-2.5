<?php
/**
* MySMS - Simple SMS Component for Joomla
*
* Axel Sauerhoefer < mysms[at]quelloffen.com >
*
* http://www.willcodejoomlaforfood.de
*
* $Author: axel $
* $Rev: 211 $
* $HeadURL: svn://willcodejoomlaforfood.de/mysms/branch/1.5.x/administrator/components/com_mysms/admin.mysms.html.php $
*
* $Id: admin.mysms.html.php 211 2010-04-11 12:16:50Z axel $
*
* All rights reserved. 
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* MySMS! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
**/
//check if joomla call us
defined( '_JEXEC' ) or die( 'Restricted access' );

if( defined( 'BYJG_BACKEND_ADMIN_HTML_PHP' ) == true )
{
  return;
}

/**
 * Define our class constant to precent multipe definition
 */
define( 'BYJG_BACKEND_ADMIN_HTML_PHP', 1 );


/**
*  HTML_BYJG is the html backend class from com_BYJG
*
* @package BYJG
* @subpackage Backend
**/
class ByJGBackendHtml
{


/**
* This function shows the user overview panel
*
*  @param array $rows
*  @param array $ByJGRows
*  @param array $pageNav
**/

function showUser($rows, $ByJGRows, $pageNav)
{
	$option = JRequest::getCmd('option');
	?>
<style type="text/css">
<!--
.style1 {
	color: #990000;
	font-weight: bold;
}
.style2 {color: #FF0000}
-->
</style>

    <form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap><?php echo JText::_( 'BYJG_USER_ADMIN' );?></th>
	  </tr>
	  </table>

	  <table class="adminlist">
		<tr>
			<th width="5">
			#
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title"><?php echo JText::_( 'BYJG_USERID' );?></th>
			<th class="title"><?php echo JText::_( 'BYJG_NAME' );?></th>
			<th class="title"><?php echo JText::_( 'BYJG_USERNAME' );?></th>
			<th class="title"><?php echo JText::_( 'BYJG_ALLOWED_SEND_SMS' );?></th>
			<th class="title"><?php echo JText::_( 'BYJG_CREDITS' );?></th>
	  </tr>
		<?php
		$k = 0;
		 $allow = false;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			?>
			<tr>
			  <td><?php echo $pageNav->getRowOffset( $i ); ?></td>
			  <td><?php echo JHTML::_('grid.id', $i, $row->id) ?></td>
			  <td><?php echo $row->id;?></td>
			  <td><?php echo $row->name ?></td>
			  <td><?php echo $row->username; ?></td>


			  <?php
                            $credits = 0;
                            $allow = false;
			    foreach($ByJGRows as $s ){
                              if( $s->userid == $row->id && $s->state==1){
                                $credits = $s->credits;
                                $allow = true;
                                break;
                              }
                            }
			  ?>



			  <td >
  			     <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php if($allow==true){echo 'unpublish';}else{echo'publish';}?>')">
				<img src="images/<?php echo ($allow ? 'tick.png' : 'publish_x.png');?>" width="12" height="12" border="0" alt="allow <?php echo $row->name; ?>" />
		        </a>
		      <td><?php echo $credits; ?></td>
			</td>
			</tr>
			<?php
		}
		?>
	  </table>

	  <?php echo $pageNav->getListFooter(); ?>

	  <input type="hidden" name="option" value="<?php echo $option; ?>" />
	  <input type="hidden" name="act" value="user" />
	  <input type="hidden" name="task" value="" />
	  <input type="hidden" name="boxchecked" value="0" />
	  <input type="hidden" name="hidemainmenu" value="0" />
</form>
<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
}

 function showCredits()
 { 	
    $option = JRequest::getCmd('option');
?>
    <table class="adminheading">
           <tr>
	       <th class="cpanel" rowspan="2" nowrap>SMS BYJG</th>
	  </tr>
   </table>

    <table cellpadding="4" cellspacing="0" border="0"  class="adminform">
	<tr valign="top">
		<td>
			<div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=provider" >
								<img alt="<?php echo JText::_( 'BYJG_PROVIDER_ADMIN' );?>" src="images/browser.png" alt="Provider" align="middle" name="image" border="0"/><br/>
							<?php echo JText::_( 'BYJG_PROVIDER_ADMIN' );?>
					  </a>
				  </div>
			  </div>
		  </div>
			</div>
			
			<div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=user">
								<img alt="<?php echo JText::_( 'BYJG_USER_ADMIN' ) ;?>" src="images/addusers.png" alt="Manage User" align="middle" name="image" border="0" /><br/>
							<?php echo JText::_( 'BYJG_USER_ADMIN' );?> </a>
				  </div>
			  </div>
		  </div>
			</div>
			<div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=ad">
								<img alt="<?php echo JText::_('BYJG_ADVERTISMENT' ); ?>" src="images/addedit.png" align="middle" name="image" border="0" /><br/>
							<?php echo JText::_('BYJG_ADVERTISMENT' ); ?> </a>
				  </div>
			  </div>
		  </div>
			</div>
			<div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=global">
							<div class="iconimage">
								<img  alt="<?php echo JText::_( 'BYJG_GLOBAL_SETTINGS' ); ?>" src="images/impressions.png" align="middle" name="image" border="0" />
							</div>
							<?php echo JText::_( 'BYJG_GLOBAL_SETTINGS' ); ?> </a>
					</div>
				</div>
			</div>
			<div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=about">
							<div class="iconimage">
								<img alt="<?php echo JText::_( 'BYJG_SHOW_ABOUT' ); ?>" src="images/install.png" align="middle" name="image" border="0" />
							</div>
							<?php echo JText::_( 'BYJG_SHOW_ABOUT' ); ?> </a>
					</div>
				</div>
			</div>
		  <div id="cpanel">
				<div style="float:left;">
					<div class="icon">
						<a href="index.php?option=com_byjg&amp;act=prereq">
							<div class="iconimage">
								<img alt="<?php echo JText::_( 'BYJG_PREREQ_CHECK' ); ?>" src="images/systeminfo.png" align="middle" name="image" border="0" />
							</div>
							<?php echo JText::_( 'BYJG_PREREQ_CHECK' ); ?> </a>
					</div>
				</div>
		  </div>
			</div>
		</td>
	</tr>
</table>
<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
}

/**
* This function shows the edit user panel
*
*  @param array $rows
*  @param array $ByJGRows
**/
function editUser(&$row, &$ByJGRow)
  {
    $option = JRequest::getCmd('option');

  ?>
              <form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap><?php echo $row->name .' '. JText::_( 'BYJG_EDIT' ); ?></th>
	  </tr>
	  </table>

	  <table cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td valign="top">
    	  <table class="adminlist">
    		<tr>
    			<th class="title" colspan="2">Detalhes</th>
    	  </tr>
    	  <tr>
    	    <td width="50"><?php echo JText::_( 'BYJG_PHONENUMBER' );?></td>
    	    <td><input type="text" name="number" value="<?php echo $ByJGRow->number; ?>" size="50" /></td>
    	  </tr>
    	  <tr>
    	    <td><?php echo JText::_( 'BYJG_COMMENT' ); ?></td>
    	    <td><input type="text" name="comment" value="<?php echo $ByJGRow->comment ?>" size="50" /></td>
    	  </tr>
    	  <tr>
    	    <td><?php echo JText::_( 'BYJG_CREDITS' );?></td>
    	    <td><input type="text" name="credits" value="<?php echo $ByJGRow->credits ?>" size="50" /></td>
    	  </tr>
    	  </table>
    	</td>
    </tr>
    </table>
	  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	  <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="user" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		
		<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
}




  /**
* This function shows the edit user panel
*
*  @param array $rows
*  @param array $ByJGRows
**/
function showAdvertisment(&$ad)
  {
    $option = JRequest::getCmd('option');
  ?>
   <form action="index.php" method="post" name="adminForm">

	 <table class="adminheading">
	<tr>
			<th class="edit" rowspan="2" nowrap><?php echo JText::_('BYJG_ADVERTISMENT' ); ?></th>
	  </tr>
     </table>

	  <table cellspacing="0" cellpadding="0" width="100%" >
		<tr valign="top">
			<td valign="top">
    	  		<table class="adminlist">
    	  			<tr>
              			<textarea rows="3" cols="40" name="ad" id="ad"><?php echo $ad; ?></textarea>
    	  			</tr>
    			</table>
    		</td>
   		</tr>
	 </table>

	    <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="ad" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
</form>
<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
<?php
}


/**
* This function shows a provider selection panel before editing a provider
*
*  @param array $rows
**/
function showProviderSelectPanel(&$rows)
{
      $option = JRequest::getCmd('option');
?>
        <form action="index.php" method="post" name="adminForm">
          <table class="adminheading">
		<tr>
	 	    <th class="edit" rowspan="2" nowrap="nowrap">Administrar Provedor<br /></th>
	       </tr>
	  </table>

	  <table class="adminlist">
	    <tr>
			<br />
			<div id="alerta" style="width: 85%; text-align: center; margin: 0 auto; border:#0B55C4 dotted 1px; background-color:#F6F6F6; padding: 5px;">
			<strong>IMPORTANTE:</strong> Este componente é distribuido gratuitamente. Pedimos apenas que ao criar sua conta nos provedores disponibilizados, 
			siga a <a href="index.php?option=com_byjg&amp;act=about" target="_self">orientação</a> na area da ajuda. Somente dessa maneira conseguiremos enxergar que o seu acesso ocorre pelo componente SMS BYJG e assim propiciar meios para que este componente possa continuar melhorando.
			</div>
			<br />&nbsp;Para adicionar ou substituir novos gateways, veja mais informa&ccedil;&otilde;es na se&ccedil;&atilde;o: "<a href="index.php?option=com_byjg&amp;act=about" target="_self">AJUDA - Gateways</a>".<br />
		</tr>
		<tr>
			<th width="5">#</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title"><?php echo JText::_( 'BYJG_PROVIDER' );?></th>
			<th class="title"><?php echo JText::_('BYJG_ACTIV' );?></th>
	    </tr>
		<?php
		$k = 0;
		$allow = false;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			?>
			<tr>
			  <td></td>
			  <td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
			  <td>
			  <?php 
			  	echo $row->name;
				if ($row->name == 'ByJG') {
					$factory = new ProviderFactory();
					$provider = $factory->getActiveInstance();
					$msgBalance = '';
					$retornoBalance = $provider->recoverBalance($msgBalance);
					echo $retornoBalance ;
				}
			  ?>
			  </td>
			  <td>
			  	<?php 
					$status_provider = $row->active;
					if ($status_provider == 1) {
						?>
						<img src="images/tick.png" width="16" height="16" border="0" alt="SMS Gateway Ativo"  title="SMS Gateway Ativo"/>
						<?php
					} elseif ($status_provider == 0) {
						?>
						<img src="images/publish_x.png" width="16" height="16" border="0" alt="SMS Gateway Inativo" title="SMS Gateway Inativo"/>
						<?php
					}
				?>
			  </td>
			</tr>
			<?php
		}
		?>
	  </table>

	  <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="provider" />
		<input type="hidden" name="task" value="configure" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
        <?php
  }

/**
* This function shows a provider edit panel
*
*  @param array $row
*  @param object $provider
**/

function editProvider(&$row, &$provider)
  {

    $option = JRequest::getCmd('option');
  ?>
     <form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap="nowrap"><?php echo JText::_( 'BYJG_EDIT' ) .' provedor '. $row->name ;?></th>
	  </tr>
	  </table>

	  <table cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td valign="top">
    	  		<table width="100%" class="adminlist">
    				<tr>
    					<th colspan="2" align="left" class="title">
    						<?php echo JText::_( 'BYJG_DETAILS' ) ;?></th>
          			</tr>
<?php

          //create dynamic html from params array

          $tmp = '';
          foreach($provider->_params as $key=>$val )
          {
             $tmp .= "$key,";
?>
          <tr>
          <td width="190" align="right">
          	<strong>&nbsp;&nbsp;
		  	<?php  
				if ($key == 'username') {
					$text_key = 'Usuario';
				} elseif ($key == 'password') {
					$text_key = 'Senha';
				} else {
					$text_key = $key;
				}
				echo $text_key; 
			?>
			:&nbsp;&nbsp;
			</strong>
		  </td>
			  <td align="left">
			  	<?php
				if ($key == 'username') {
					echo '<input type="text" name="'.$key.'" value="'.$val.'"> OBS.: sua conta no gateway deve ser criada com os 4 primeiros caracteres igual a "pixx". <a href="index.php?option=com_byjg&amp;act=about" target="_self">Veja detalhes </a>';
				} else {
					echo '<input type="text" name="'.$key.'" value="'.$val.'">';
				}				
				?>
			  </td>
			  
          </tr>

<?php

           }


?>


    	  </table>
    	</td>
    </tr>
    </table>
	  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	  <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="provider" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="providerparams" value="<?php echo $tmp;?>" />

</form>
<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
	  <?php
  }



function showLoadPanel(&$cid)
 {
    $option = JRequest::getCmd('option');
?>
        <form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap="nowrap"><?php echo JText::_( 'BYJG_LOADLIST' ); ?></th>
	  </tr>
	  </table>

	  <table cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td valign="top">
    	  <table class="adminlist">
    		<tr>
    			<th class="title" colspan="2"></th>
          </tr>
		  
		  <tr>
			<td><b><?php echo JText::_( 'BYJG_SELECTED_USER' );?></b><br/>
			<ul>
			<?php
				foreach( $cid as $user ){
					$u = new ByJGUser( $user );
					echo "<li>" . $u->userName() . "</li>";
					unset($u);
				}
			?>
		  </ul>
			</td>
			</tr>
          
          <tr>
          	<td>
          		<b>
          			<?php echo JText::_( 'BYJG_NEW_CREDITS' );?>:
          		</b>
          		<input type="text" name="credits" value="0" size="4">
          		<input type="submit" name="button" value="<?php echo JText::_( 'BYJG_SAVE' );?>">
          	</td>
          </tr>
			

    	  </table>
    	</td>
    </tr>
    </table>

<?php
		foreach( $cid as $id )
		{
			echo '<input type="hidden" name="cid[]" value="'. $id.'" />';
		}
?>

	  <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="user" />
		<input type="hidden" name="task" value="loadList" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
	  <?php
  }

function showGlobal( $config )
{
	$option = JRequest::getCmd('option');
?>
        <form action="index.php" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th class="edit" rowspan="2" nowrap="nowrap"><?php echo JText::_( 'BYJG_GLOBAL_CONFIG' ); ?></th>
	  			</tr>
	  		</table>
	  		<table cellspacing="0" cellpadding="0" width="100%">
				<tr valign="top">
					<td valign="top">
    	  				<table class="adminlist">
    						<tr>
    							<th class="title" colspan="2"></th>
          					</tr>
<?php
		foreach( $config as $key => $val )
		{
			$friendly  = $val['friendly'];
			$type  = $val['type'];
			$value = $val['value'];
			
				echo '<tr>
						<td width="200" >';
							echo $friendly;
					echo '</td>
							<td>';
					
							 if( $type == 'text' )
							 {				
							 	echo "<input type=\"text\" name=\"config[$key]\" value=\"$value\" />";
							 }
							 
							 
							 if( $type == 'textarea' )
							 {
							 	echo "<textarea cols=\"60\" rows=\"5\" name=\"config[$key]\">$value</textarea>";
							 }
							 
				echo '</td>
						</tr>';
		}
?>
    	  				</table>
    				</td>
    			</tr>
    		</table>

	  		<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="act" value="global" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
	  <?php
}

	/**
	 * Show some links and statistics about com_byjg
	 */
	function showAbout()
	{
	?>
			<table class="adminheading">
				<tr>
					<th class="edit" rowspan="2" nowrap="nowrap"><?php echo JText::_( 'BYJG_SHOW_ABOUT' ); ?></th>
	  			</tr>
	  		</table>

	  		<!-- show install stuff -->
			<table width="90%" border="0" align="center" cellpadding="6" cellspacing="0">
				<tr>
					<td width="50%" align="left" valign="top">
						<p><a href="http://www.byjg.com.br" target="_blank" alt="BYJG Tecnologia">
						  <img src="http://www.byjg.com.br/imagens/logo-byjg-topoParaWeb.png" alt="SMS BYJG - Desenvolvido por BYJG Tecnologia" border="0"></a></p>
						<p>&nbsp; </p></td>
				    <td width="25%" align="center" valign="middle">&nbsp;</td>
				    <td width="25%" align="center" valign="middle">Este componente JOOMLA! <br />
foi baseado no <a href="http://mysms.joomlacoder.de" target="_blank">MySMS</a>.</td>
				</tr>
				<tr>
				  <td colspan="3" align="left" valign="top"><div id="div" style="width: 95%; padding: 5px; text-align: center; margin: 0 auto; border:#990000 dotted 1px; background-color:#FFCCCC;">
                    <p><span style="text-align:center;"><span style="color: #FF0000;"><strong>IMPORTANTE:</strong></span> <a name="alertapixxi" id="alertapixxi"></a>Este componente é distribuido gratuitamente. Pedimos apenas que ao criar sua conta nos provedores disponibilizados neste compoente, solicite que seu <strong>USUARIO</strong> inicie com os 4 (quatro) primeiro caracteres iguais a <strong>&quot;pixx&quot;</strong>. Por exemplo, se você deseja que sua conta seja &quot;<em>usuario</em>&quot;, solicite a criação da conta &quot;<em>pixxusuario</em>&quot;. <strong>Isto é um pedido, não é uma regra</strong>. Porém caso opte por não criar a conta seguindo esta orientação, <strong>você deve remover</strong> por sua conta e risco, o trecho no código PHP que verifica se esta condição foi atendida para liberar o envio do SMS. </span></p>
				    <p><span style="text-align:center;">Aceitando esta  simples regra, temos como   enxergar o  uso deste componente e assim propiciar meios para que este componente possa continuar melhorando através de parcerias com os respectivos gateways. </span></p>
			      </div>
			      <p>&nbsp;</p></td>
			  </tr>
				<tr>
					<td align="left" valign="top" bgcolor="#EFEFEF">
						<h3>Obrigado por usar o SMS BYJG</h3>
						
						Veja alguns links interessantes ligados a este projeto:
						<ul>
							<li>
								BYJG Tecnologia: <a href="http://www.byjg.com.br/" target="_blank" alt="BYJG Tecnologia">http://www.byjg.com.br</a>							</li>
							<li> 
								Vídeo tutorial: <a href="http://www.byjoomla.com.br" target="_blank" alt="joomlers">http://www.byjoomla.com.br</a></li>
						</ul>
						
						<p>&nbsp;</p>					</td>
				    <td colspan="2" align="left" valign="top" bgcolor="#EFEFEF"><p>&nbsp;</p>
			        <p>&nbsp;</p>
			        <ul>
			          <li> Site oficial do MySMS: <a href="http://www.willcodejoomlaforfood.de" alt="willcodejoomlaforfood">http://www.willcodejoomlaforfood.de</a> </li>
			          <li>Gateway SMS ByJG (gateway oficial): <a href="http://www.byjg.com.br/site/xmlnuke.php?module=byjg.login&amp;action=action.NEWUSER&amp;idrevenda=2290" target="_blank" alt="joomlers">http://www.byjg.com.br</a></li>
		            </ul>			        
			        <p align="right">SMS BYJG utiliza algumas imagens sob licenca GPL de <a href="http://www.everaldo.com/crystal/" target="_blank"> Everaldo - Crystal </a></p>		            </td>
				</tr>
				<tr>
				  <td colspan="3" align="left" valign="top"><h3><br />
			          O COMPONENTE SMS BYJG </h3>
				    <p> O principal  objetivo para o desenvolvimento deste componente foi criar um sistema que possuisse uma interface em portugu&ecirc;s do Brasil e que tivesse  as seguintes caracter&iacute;sticas:</p>
				    <ul>
				      <li>pagamento de recarga em moeda do Brasil (R$).</li>
			          <li>modalidades de pagamento acess&iacute;veis ao p&uacute;blico em geral (boleto, cart&atilde;o de cr&eacute;dito, MOIP, PayPal).     </li>
			          <li>cobertura no territ&oacute;rio brasileiro atrav&eacute;s das principais operadoras de telefonia celular atuante no pa&iacute;s: Tim, Oi, Vivo e Claro.</li>
				    </ul>
				    <p>Através de uma parceria com o Provedor de Serviços SMS ByJG este componente tomou vida e, por padr&atilde;o, o SMS BYJG vem configurado para trabalhar  com o gateway ByJG mas pode ser alterado por sua conta e risco para novos gateways (nacionais ou estrangeiros) ou atrav&eacute;s de contrata&ccedil;&atilde;o de servi&ccedil;o de personaliza&ccedil;&atilde;o junto &agrave; <a href="http://www.byjg.com.br/" target="_blank">BYJG TECNOLOGIA</a> a ser or&ccedil;ado de acordo com suas necessidades.      </p>
				    <br />
				    <h3>AJUDA PARA USAR O SMS BYJG </h3>
				    <h4>1 -  Configura&ccedil;&otilde;es Iniciais </h4>
				    <p>Ao instalar o componente &eacute; necess&aacute;rio fazer alguns ajustes. Vamos ver agora atrav&eacute;s de um passo-a-passo as primeiras configura&ccedil;&otilde;es para deixar o seu SMS BYJG funcionando.  </p>
				    <p><strong>2 - Administrar Provedor</strong></p>
				    <p>Por padr&atilde;o, o SMS BYJG est&aacute; configurado para trabalhar somente com um gateway que tem cobertura dentro do territ&oacute;rio brasileiro atuando com as principais operadoras (Tim, Oi, Vivo e Claro) e que trabalhe com formas de pagamento em moeda brasileira (R$). Este foi o objetivo de sua cria&ccedil;&atilde;o: um sistema que tenha sua interface em portugu&ecirc;s do Brasil e que tenha formas de recarga em moeda do Brasil.</p>
				    <p><span class="style1">Nota:</span> A BYJG TECNOLOGIA oferece o servi&ccedil;o de personaliza&ccedil;&atilde;o e desenvolvimento para este produto, consulte atrav&eacute;s do e-mail <em><strong>sms@byjg.com.br</strong></em>.    </p>
				    <p>Para chegar a tela de <strong>Administrar Provedor</strong> v&aacute; ao menu Componentes -&gt;  SMS BYJG -&gt; Administrar Provedor. </p>
				    <p>Selecione a caixa que est&aacute; posicionada ao lado esquerdo do nome do seu provedor e clique sobre o bot&atilde;o EDITAR na barra de ferramentas (superior direito).</p>
				    <p>Na tela seguinte, insira ou altere os dados nas caixas de texto. As caixas variam de acordo com o provedor que voc&ecirc; estiver usando/editando, mas por padr&atilde;o, o provedor oficial do SMS BYJG ir&aacute; solicitar apenas um nome de usu&aacute;rio e sua respectiva senha. Ao concluir, clique no bot&atilde;o SALVAR na barra de ferramentas.      </p>
				    <p><strong>3 - Administrar Usu&aacute;rio</strong></p>
				    <blockquote>
				      <p><strong>3.1 - Criar Novo Usu&aacute;rio</strong></p>
				      <p>A cria&ccedil;&atilde;o de novos usu&aacute;rios &eacute; feita atrav&eacute;s da rotina de Administra&ccedil;&atilde;o de Usu&aacute;rios do pr&oacute;prio JOOMLA!. Crie novos usu&aacute;rios ou solicite aos visitantes que se cadastrem no seu site para que possa dar acesso aos mesmos para usar o SMS BYJG em seu site.       </p>
				      <p><strong>3.1 - Administrar  Usu&aacute;rio Existente</strong></p>
				      <p>Antes de qualquer ação sobre um usuário, ao acessar o painel de adminsitração de usuários, clique sobre o ícone que indica que o mesmo está &quot;despuplicado&quot; (inativo) para que este seja &quot;publicado&quot; (se torne ativo). </p>
				      <p>Após ativar o usuário, para gerenciá-lo, marque a caixa ao lado esquerdo do nome do usuário e então clique sobre o botão EDITAR na barra de ferramentas. Esta ação deve ser a primeira coisa a fazer antes de informar ao usuário que ele pode usar o componente. Essa ação é necessário pois é nesta tela que você cadastra o contato (telefone) deste usuário, sem o qual ele não conseguirá enviar SMS pois o sistema (frontend) faz essa critica. Isso ajuda a evitar que um usuário se cadastre e passe a utilizar o sistema indevidamente.</p>
			        </blockquote>
				    <p><strong>4 - Avisos</strong></p>
				    <p>Esta funcionalidade ainda não está concluida nesta versão, mas servirá para que o administrador insira um texto que irá ser enviado junto a todas as mensagens. Pode ser usado para uma assinatura do serviço ou uma informação crucial.</p>
				    <p><strong>5 - Configuração Global</strong> </p>
				    <p>Configurações globais sobre o sistema. </p>				    <p>&nbsp;</p></td>
			  </tr>
				<tr>
				  <td colspan="3" align="left" valign="top"><h3>Solicitar Altera&ccedil;&otilde;es ou Novas Funcionalidades </h3>
				    <p>Para solicitar altera&ccedil;&otilde;es ou inclus&atilde;o de novas funcionalidades para o SMS BYJG, envie um email para <em><strong>sms@byjg.com.br</strong></em> com detalhes sobre sua necessidade. Retornaremos com a informa&ccedil;&atilde;o se a funcionalidade j&aacute; est&aacute; em fase de desenvolvimento ou em casos espec&iacute;ficos, retornaremos com um or&ccedil;amento para que possa avaliar e aprovar.      </p>
				    <h3>Changelog</h3>
				    <p>Acompanhe as vers&otilde;es do SMS BYJG que j&aacute; foram disponibilizadas: </p>
				    <table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                      <tr>
                        <td width="10%"><div align="center">Vers&atilde;o</div></td>
                        <td width="11%"><div align="center">Em</div></td>
                        <td width="79%"><div align="center">Altera&ccedil;&atilde;o</div></td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.0</td>
                        <td align="center" valign="top">05/10/2010</td>
                        <td align="left" valign="top">Lan&ccedil;amento do componente.</td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.1</td>
                        <td align="center" valign="top">06/10/2010</td>
                        <td align="left" valign="top">Acerto das rotinas archiveSMS() e sendSMS().</td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.2</td>
                        <td align="center" valign="top">09/10/2010</td>
                        <td align="left" valign="top">Inclus&atilde;o do campo DDD separado no frontend. </td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.3</td>
                        <td align="center" valign="top">10/10/2010</td>
                        <td align="left" valign="top">Inserido informa&ccedil;&atilde;o de saldo de cr&eacute;ditos capturado autom&aacute;ticamente junto ao provedor (v&aacute;lido somente para o provedor padr&atilde;o do SMS BYJG   . </td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.4</td>
                        <td align="center" valign="top">10/10/2010</td>
                        <td align="left" valign="top">Resolvido bug´s da instalação, envio de sms. </td>
                      </tr>
                      <tr>
                        <td align="left" valign="top">1.0.5</td>
                        <td align="center" valign="top">13/10/2010</td>
                        <td align="left" valign="top">Visibilidade do historico de mensagens enviadas </td>
                      </tr>
                    </table>			        
		          <p>&nbsp;</p></td>
			  </tr>
			</table>
			<table style="width: 100%;">
	<tr>
		<td style="text-align: center;">
			<div id="assinatura">
				<div style="float:left; width: 100%; text-align: center;">
					<br /><img src="http://www.byjg.com.br/imagens/logo-byjg-joomla.png" alt="SMS BYJG - ByJG Tecnologia e Sistemas" title="SMS BYJG - ByJG Tecnologia e Sistemas" width="250" height="35" />
				</div>
			</div>
		</td>
	</tr>
</table>
	<?php
	}

	/**
	 * Show some links and statistics about com_byjg
	 */
	function showPrerequisite( &$data )
	{
	?>
			<table class="adminheading">
				<tr>
					<th class="edit" rowspan="2" nowrap="nowrap"><?php echo JText::_( 'BYJG_PREREQ_CHECK' ); ?></th>
	  			</tr>
	  		</table>
	<?php
			while( ($entry = array_pop($data) ) !== null  ){
				list( $key, $val, $desc ) = $entry;

				$style = 'background-color: #AAFFAA; padding: 5px;';

				if( $val == false ){
					$style= 'background-color: #FFA98C; padding: 5px;';
				}

				echo "<table class=\"adminlist\" style=\"$style\">
						<tr>
							<td width=\"25%\">$key</td>
							<td width=\"25%\">$val</td>
							<td width=\"50%\">$desc</td>
						</tr>
					</table>";
			}
	}


}     //end class
?>
