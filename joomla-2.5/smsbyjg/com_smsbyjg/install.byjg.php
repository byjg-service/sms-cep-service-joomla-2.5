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



function com_install()
{
?>
<table border="0" width="100%">
<tr>
  <td width="50%" align="left" valign="top"><p><a href="http://www.byjg.com.br" target="_blank" alt="BYJG Tecnologia"> <img src="http://www.byjg.com.br/imagens/logo-byjg-topoParaWeb.png" alt="SMS BYJG - Desenvolvido por BYJG Tecnologia" border="0" /></a></p>
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
  <td align="left" valign="top" bgcolor="#EFEFEF"><h3>Obrigado por usar o SMS BYJG</h3>
    Veja alguns links interessantes ligados a este projeto:
    <ul>
        <li> BYJG Tecnologia: <a href="http://www.byjg.com.br/" target="_blank" alt="BYJG Tecnologia">http://www.byjg.com.br</a> </li>
      <li> Vídeo tutorial: <a href="http://www.byjoomla.com.br" target="_blank" alt="joomlers">http://www.byjoomla.com.br</a></li>
    </ul>
    <p>&nbsp;</p></td>
  <td colspan="2" align="left" valign="top" bgcolor="#EFEFEF"><p>&nbsp;</p>
      <p>&nbsp;</p>
    <ul>
        <li> Site oficial do MySMS: <a href="http://www.willcodejoomlaforfood.de" alt="willcodejoomlaforfood">http://www.willcodejoomlaforfood.de</a> </li>
      <li>Gateway SMS ByJG (gateway oficial): <a href="http://www.byjg.com.br/site/xmlnuke.php?module=byjg.login&amp;action=action.NEWUSER&amp;idrevenda=2290" target="_blank" alt="joomlers">http://www.byjg.com.br</a></li>
    </ul>
    <p align="right">SMS BYJG utiliza algumas imagens sob licenca GPL de <a href="http://www.everaldo.com/crystal/" target="_blank"> Everaldo - Crystal </a></p></td>
</tr>
<tr>
  <td colspan="3" align="left" valign="top"><h3>&nbsp;</h3>
    <h3>O COMPONENTE SMS BYJG </h3>
    <p> O principal  objetivo para o desenvolvimento deste componente foi criar um sistema que possuisse uma interface em portugu&ecirc;s do Brasil e que tivesse  as seguintes caracter&iacute;sticas:</p>
    <ul>
      <li>pagamento de recarga em moeda do Brasil (R$).</li>
      <li>modalidades de pagamento acess&iacute;veis ao p&uacute;blico em geral (boleto, cart&atilde;o de cr&eacute;dito, MOIP, PayPal). </li>
      <li>cobertura no territ&oacute;rio brasileiro atrav&eacute;s das principais operadoras de telefonia celular atuante no pa&iacute;s: Tim, Oi, Vivo e Claro.</li>
    </ul>
    <p>Através de uma parceria com o Provedor de Serviços SMS ByJG este componente tomou vida e, por padr&atilde;o, o SMS BYJG vem configurado para trabalhar  com o gateway ByJG mas pode ser alterado por sua conta e risco para novos gateways (nacionais ou estrangeiros) ou atrav&eacute;s de contrata&ccedil;&atilde;o de servi&ccedil;o de personaliza&ccedil;&atilde;o junto &agrave; <a href="http://www.byjg.com.br/" target="_blank">BYJG TECNOLOGIA</a> a ser or&ccedil;ado de acordo com suas necessidades. </p>
    <p><strong>Acesse:</strong> COMPONENTE -&gt; SMS BYJG -&gt; PAINEL DE CONTROLE</p></td>
</tr>
</table>     
<?php
}
?>