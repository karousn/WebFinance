<script type="text/javascript">
   function confirmDeleteTaxe(id,txt) {
  if (confirm(txt)) {
    window.location = 'save_paybox.php?action=delete&id='+id;
  }
}
</script>

<?php
$res = mysql_query("SELECT id_pref, type_pref, value FROM webfinance_pref WHERE type_pref='paybox'")
   or wf_mysqldie();
if(mysql_num_rows($res)>0){
  list($id_pref,$type_pref,$value) = mysql_fetch_array($res);
  $paybox = unserialize(base64_decode($value));
 }else{
  $id_pref=-1;

  $paybox->PBX_SITE=1999888;
  $paybox->PBX_RANG=99;
  $paybox->PBX_IDENTIFIANT=2;
 }
?>
<form action="save_paybox.php" method="get">
 <input type="hidden" name="id" value="<?=$id_pref?>"/>

<table border="0" cellspacing="0" cellpadding="3" class="framed">
<tr style="text-align: center;" class="row_header">
  <td><?= _('Variable') ?></td>
  <td><?= _('Value') ?></td>
</tr>
  <tr class="row_even">
   <td>PBX_SITE</td><td><input type="text" name="PBX_SITE" value="<?=$paybox->PBX_SITE?>" /></td>
  </tr>
  <tr class="row_even">
   <td>PBX_RANG</td><td><input type="text" name="PBX_RANG" value="<?=$paybox->PBX_RANG?>" /></td>
  </tr>
  <tr class="row_even">
   <td>PBX_IDENTIFIANT</td><td><input type="text" name="PBX_IDENTIFIANT" value="<?=$paybox->PBX_IDENTIFIANT?>" /></td>
  </tr>
<tr class="row_even">
  <td style="text-align: center;" colspan="3"><input type="submit" value="<?= _('Save') ?>" /></td>
</table>

</form>
