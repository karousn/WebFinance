<?php
/*
 Copyright (C) 2004-2006 NBI SARL, ISVTEC SARL

   This file is part of Webfinance.

   Webfinance is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

    Webfinance is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Webfinance; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require("../inc/main.php");

if(isset($_POST['action'],$_POST['id'],$_POST['mails2']) &&
   $_POST['action'] == 'send' && is_numeric($_POST['id'])) {

	if(!isset($_POST['action'],$_POST['id']) or $_POST['action'] != 'send'
	   or !is_numeric($_POST['id']))
		die(_("Error: Missing invoice id"));

	$id_invoice = $_POST['id'];

	$mails = array();
	if( isset($_POST['mails']) ){
		foreach($_POST['mails'] as $m ){
			$m = trim($m);
			if(strlen($m)>0)
				$mails[]=$m;
		}
    }

	$_POST['mails2']=str_replace(';',',',$_POST['mails2']);
	$mail_addresses = explode(',',$_POST['mails2']);
	foreach($mail_addresses as $m ){
		$m = trim($m);
		if(strlen($m)>0)
			$mails[]=$m;
	}

	if(count($mails)==0){
		echo _("Please add mail address!");
		exit;
	}


	$from='';
    if(preg_match('/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-Za-z]{2,4}$/',
				  $_POST['from']))
		$from = $_POST['from'];

	$fromname = $_POST['from_name'];

	$subject = stripslashes($_POST['subject']) ;
    $body = stripslashes($_POST['body']) ;

	$docs 				= false; 

	$invoice = new Facture;	
	
	if($_POST['docs'] == 1) $docs = true; 
		
	if(!$invoice->sendByEmail($id_invoice, $mails, $from, $fromname, $subject,
							  $body, false, $docs)) {
		$_SESSION['message'] = _('Invoice was not sent');
		$_SESSION['error'] = 1;
		echo _("Invoice was not sent");
		die();
    }

	/* $_SESSION['message'] = _('Invoice sent'); */
	//mettre à jour l'état de la facture, update sql
	mysql_query("UPDATE webfinance_invoices ".
				"SET is_envoye=1 ".
				"WHERE id_facture=$id_invoice")
		or wf_mysqldie();
	/* $_SESSION['message'] .= "<br/>"._('Invoice updated'); */

	$facture = $invoice->getInfos($id_invoice);
	logmessage(_("Send invoice")." #$facture->num_facture fa:$id_invoice ".
			   "client:$facture->id_client");

    header("Location: edit_facture.php?id_facture=$id_invoice");
    die();
}


$title = _("Send Invoice");
must_login();
$roles = 'manager,admin';
require("../top.php");
require("nav.php");

?>

<script type="text/javascript" language="javascript"
  src="/js/ask_confirmation.js"></script>

<?php
extract($_GET);
$mails=array();

//Récupérer les adresses mails:
$result = mysql_query(
"SELECT i.id_client as id_client, email, nom
FROM webfinance_clients c
LEFT JOIN webfinance_invoices i ON (c.id_client = i.id_client)
WHERE id_facture=$id")
  or wf_mysqldie();
$client=mysql_fetch_assoc($result);
mysql_free_result($result);

$Client = new Client($client['id_client']);

if(!empty($client['email'])){
  $emails = explode(',',$client['email']);
  $i = 1;
  foreach($emails as $email){
    $mails[$i." - ".$client['nom']] = $email;
    $i++;
  }
 }

//récupérer les info sur la société
$result = mysql_query("SELECT value FROM webfinance_pref WHERE type_pref='societe' AND owner=-1")
  or wf_mysqldie();
list($value) = mysql_fetch_array($result);
mysql_free_result($result);
$societe = unserialize(base64_decode($value));

//récupération des infos sur la facture
$Facture = new Facture();
$invoice = $Facture->getInfos($id);

$docs = false;
if(isset($_GET['docs'])) $docs = 1;
?>

<form id="main_form" method="post">
  <input type="hidden" name="action" value="send">
  <input type="hidden" name="id" value="<?= $id ?>">
  <input type="hidden" name="docs" value="<?=$docs?>">
  <table class="bordered" border="0" cellspacing="0" cellpadding="3" width="500">
  <tr>
    <td>From</td>
    <td>
  <input type="text" name="from_name" style="width: 190px;" value="<?=$societe->raison_sociale?>"/>&nbsp;
  <input type="text" name="from" style="width: 190px;" value="<?=$societe->email?>"/>
    </td>
  <tr>
  <tr>
   <td><?=_('Recipient')?></td>
   <td>
  <?
  if(count($mails)<1)
    echo _("You must add a mail address!");
  else{
    foreach($mails as $name => $mail)
      printf("<input type='checkbox' name='mails[]' checked value='%s' >%s < %s ><br/>",$mail, $name, $mail );
  }
  ?>
    <input type='text' name='mails2' style='width: 400px;'>
    <img src="/imgs/icons/help.png" onMouseOut="UnTip();" onmouseover="Tip('Adresses mails s&eacute;par&eacute;es par des virgules<br/>exemple: toto@exemple.com,foo@example.com');">
   </td>
  </tr>
<?php
  $type_doc = $invoice->type_doc; 
  if($invoice->language == 'en_US' AND $invoice->type_doc == 'facture') $type_doc	= 'invoice';
  if($invoice->language == 'en_US' AND $invoice->type_doc == 'devis') $type_doc	= 'quote';
  $filename=ucfirst($type_doc)."_".$invoice->num_facture."_".preg_replace("/[ ]/", "_", $invoice->nom_client).".pdf";
  $path="/tmp/".$filename;
?>
  <tr>
  <td></td>
  <td><img src='/imgs/icons/attachment.png'><a href="gen_facture.php?id=<?=$invoice->id_facture?>&docs=<?=$docs?>"><?=$filename?>
	<?
	if(isset($_GET['docs'])) echo ' + docs'; 	
	?></a>
	</td>
  </tr>
  <tr><td colspan='2'><hr/></td></tr>
<?php

$type_doc = 'invoice';
if($invoice->type_doc == 'devis') $type_doc = 'quote';

$result = mysql_query("SELECT value FROM webfinance_pref WHERE type_pref='mail_".$type_doc."_".$invoice->language."'") or wf_mysqldie();
list($data) = mysql_fetch_array($result);
$pref = unserialize(base64_decode($data));

// RIB
$result = mysql_query("SELECT value FROM webfinance_pref WHERE id_pref=".$invoice->id_compte) or wf_mysqldie();
list($cpt) = mysql_fetch_array($result);
mysql_free_result($result);
$cpt = unserialize(base64_decode($cpt));
if (!is_object($cpt)) {
  die("Impossible de generer la facture. Vous devez saisir au moins un compte bancaire dans les options pour emettre des factures");
}
foreach ($cpt as $n=>$v) {
  $cpt->$n = utf8_decode($cpt->$n);
}

//Company
$result = mysql_query("SELECT value FROM webfinance_pref WHERE type_pref='societe' AND owner=-1");
if (mysql_num_rows($result) != 1) { die(_("You didn't setup your company address and name. Go to 'Admin' and 'My company'")); }
list($value) = mysql_fetch_array($result);
mysql_free_result($result);
$societe = unserialize(base64_decode($value));
foreach ($societe as $n=>$v) {
  $societe->$n = preg_replace("/\xE2\x82\xAC/", "EUROSYMBOL", $societe->$n );
  $societe->$n = utf8_decode($societe->$n); // FPDF ne support pas l'UTF-8
  $societe->$n = preg_replace("/EUROSYMBOL/", chr(128), $societe->$n );
  $societe->$n = preg_replace("/\\\\EUR\\{([0-9.,]+)\\}/", "\\1 ".chr(128), $societe->$n );
}

//delay
$delay="";
$result = mysql_query("SELECT id, date_format(date, '%d/%m/%Y'), UNIX_TIMESTAMP(date) FROM webfinance_transactions WHERE id_invoice=".$invoice->id_facture." ORDER BY date DESC") or wf_mysqldie();
if(mysql_num_rows($result)==1){
  list($id_tr,$tr_date,$tr_ts_date) = mysql_fetch_array($result);
  if($tr_ts_date>$invoice->timestamp_date_facture)
    $delay=_('payable avant le')." $tr_date" ;
 }
mysql_free_result($result);

$patterns=array(
		'/%%LOGIN%%/',
		'/%%PASSWORD%%/',
		'/%%URL_COMPANY%%/' ,
		'/%%NUM_INVOICE%%/' ,
		'/%%CLIENT_NAME%%/',
		'/%%DELAY%%/',
		'/%%AMOUNT%%/',
		'/%%BANK%%/',
		'/%%RIB%%/',
		'/%%COMPANY%%/',
		'/%%SEPA_MNDTID%%/',
		);

$replacements=array(
		    $Client->login,
		    $Client->password,
		    $societe->wf_url,
		    $invoice->num_facture ,
		    $invoice->nom_client,
		    $delay,
		    $invoice->nice_total_ttc,
		    $cpt->banque,
		    $cpt->code_banque." ".$cpt->code_guichet." ".$cpt->compte." ".$cpt->clef." ",
		    $societe->raison_sociale,
		    $Client->sepa_mndtid,
		    );

if(isset($pref->subject) && !empty($pref->body)){
  $subject = preg_replace($patterns, $replacements, stripslashes(utf8_decode($pref->subject)) );
 }else
  $subject = ucfirst($invoice->type_doc)." #".$invoice->num_facture." pour ".$invoice->nom_client;

?>
  <tr>
   <td><?=_('Subject')?></td>
   <td>
     <input type="text" name="subject" style="width: 400px;" value="<?=$subject?>">
     <img src="/imgs/icons/help.png" onMouseOut="UnTip();" onmouseover="Tip('Personnalisez le sujet et le corps de l\'email dans:<br/>Administration > Preferences');">
   </td>
  </tr>
<tr>
  <td>Body</td>
  <td>
<textarea name="body" style="width: 400px; height: 300px; border: solid 1px #ccc;">
<?
  if(isset($pref->body) AND !empty($pref->body) )
    echo stripslashes(preg_replace($patterns, $replacements, stripslashes(utf8_decode($pref->body)) ));
  else
    echo _('Hello').",";
?>
</textarea>
  </td>
</tr>
<tr>
 <td ><a href='edit_facture.php?id_facture=<?=$invoice->id_facture?>'><?= _('Back') ?></a></td>
 <td style="text-align: center;">
  <input type="submit" value="<?= _('Send') ?>" onclick="return ask_confirmation('<?= _('Do you really want to send it?') ?>')">
 </td>

</tr>
</table>
</form>

<?php
$Revision = '$Revision: 532 $';
require("../bottom.php");
?>
