<?php
//
// This file is part of « Webfinance »
//
// Copyright (c) 2004-2006 NBI SARL
// Author : Nicolas Bouthors <nbouthors@nbi.fr>
//
// You can use and redistribute this file under the term of the GNU GPL v2.0
//
// $Id$

require("../inc/main.php");
require("../top_popup.php");

$result = mysql_query("SELECT id, id_category, text, amount, type, date, comment FROM webfinance_transactions WHERE id=".$_GET['id'])
  or die(mysql_error());
$transaction = mysql_fetch_object($result);
mysql_free_result($result);

?>
<form id="main_form" method="post" action="save_transaction.php">
<input type="hidden" name="id_transaction" value="<?= $transaction->id ?>" />
<table>

<tr>
  <td>Date</td>
  <td><input type="text" name="date" value="<?=$transaction->date ?>" size="9" /></td>
</tr>
<tr>
  <td>Category</td>
 <td>
  <select name="id_category"><?php
  $result = mysql_query("SELECT id,name FROM webfinance_categories ORDER BY name");
  while (list($id,$name) = mysql_fetch_array($result)) {
  printf('<option value="%d"%s>%s</option>', $id, ($id==$transaction->id_category)?" selected":"", $name );
 }
mysql_free_result($result);
?></select>
 </td>
</tr>
<tr>
  <td>Type</td>
  <td>
  <select name="type">
  <option value="real" <?php ("real"==$transaction->type)?" selected":""  ?> >real</option>
  <option value="prevision" <?php ("prevision"==$transaction->type)?" selected":""  ?> >prevision</option>
  <option value="asap" <?php ("asap"==$transaction->type)?" selected":""  ?> >asap</option>
  </select>
  </td>
</tr>
<tr>
  <td>Description</td>
  <td><input type="text" size="35" name="text" value="<?=$transaction->text ?>" /></td>
</tr>
<tr>
 <td>Montant :</td>
 <td>
  <input type="text" name="amount" value="<?= $transaction->amount ?>" />
 </td>
</tr>
<tr>
  <td>Comment</td>
  <td>
  <textarea rows="3" name="comment"><?=$transaction->comment?></textarea>
  </td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="Enregistrer" />
</td>
</tr>
</table>



<?php
$Revision = '$Revision$';
require("../bottom.php");
?>
