<?php
/*
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
?>
<?php
//
// This file is part of « Webfinance »
//
// Copyright (c) 2004-2006 NBI SARL
// Author : Nicolas Bouthors <nbouthors@nbi.fr>
//
// You can use and redistribute this file under the term of the GNU GPL v2.0
//
?>
<?php

# $Id: Client.php 551 2007-08-02 05:16:27Z gassla $

class Client extends WFO {
  var $id = -1;
  var $data = null;

  function _getInfos() {
    $result = $this->SQL("SELECT c.id_client as id,
                                  c.id_client, c.nom,
                                  c.date_created,
                                  c.tel, c.fax, c.web,
                                  c.addr1, c.addr2, c.addr3, c.cp, c.ville, c.pays, c.email, left(cp, 2) as departement,
                                  c.has_devis, c.has_unpaid, c.ca_total_ht, c.ca_total_ht_year, c.total_du_ht,
                                  c.vat_number, c.siren,
                                  c.id_company_type,
                                  c.id_user,
                                  c.password,
                                  ct.nom as type_name
                           FROM webfinance_clients as c, webfinance_company_types as ct
                           WHERE id_client=".$this->id) or wf_mysqldie("Client::_getInfos");
    if (mysql_num_rows($result)) {
      $data = mysql_fetch_assoc($result);
      foreach ($data as $n=>$v) { $this->$n = $v; }
      mysql_free_result($result);
    }

    // If user specified data in the siren field it can be either the RCS number
    // (format 9 digits) or the INSEE code (format : same 9 digits + 5 digits for
    // address identifier).
    // See : http://fr.wikipedia.org/wiki/Codes_INSEE

    // sensible default value
    $this->link_societe = sprintf('<a href="http://www.societe.com/cgi-bin/liste?nom=%s&dep=%s"><img src="/imgs/icons/societe.com.gif" class="bouton" onmouseover="return escape(\'%s\');" /></a>',
                                  (isset($this->nom))?urlencode($this->nom):'', (isset($this->departement))?$this->departement:'',
                                  addslashes( _('Cannot link to societe.com if no RCS or siren specified. Click icon to perform a search.') ) );
    if ( isset($this->siren) and $this->siren != "") {
      // Trim non-digits from value
      $this->siren = preg_replace("/[^0-9]/", "", $this->siren);
      switch (strlen($this->siren)) {
        case 9: // RCS
          $this->link_societe = sprintf('<a href="http://www.societe.com/cgi-bin/recherche?rncs=%s"><img src="/imgs/icons/societe.com.gif" class="bouton" onmouseover="return escape(\'%s\');" /></a>',
                                        $this->siren, addslashes( _('See financial info about this company on Societe.com') )
                                       );
          $this->siren = preg_replace("!([0-9]{3})([0-9]{3})([0-9]{3})!", '\\1 \\2 \\3', $this->siren);
          break;
        case 14: // INSEE
          $this->link_societe = sprintf('<a href="http://www.societe.com/cgi-bin/recherche?rncs=%s"><img src="/imgs/icons/societe.com.gif" class="bouton" onmouseover="return escape(\'%s\');" /></a>',
                                        substr($this->siren, 0, 9), addslashes( _('See financial info about this company on Societe.com') )
                                       );
          $this->siren = preg_replace("!([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{5})!", '\\1 \\2 \\3 \\4', $this->siren);
          break;
      }
    }

    $this->login = "";
    if(isset($this->id_user) and $this->id_user>0){
      $login_res = $this->SQL("SELECT login FROM webfinance_users WHERE id_user=".$this->id_user);
      if(mysql_num_rows($login_res)>0)
      list($this->login) = mysql_fetch_array($login_res);
    }


  }

  function Client($id = null) {
    if (is_numeric($id)) {
      $this->id = $id;
      $this->_getInfos();
    }
  }

  function setId($id) {
    if (is_numeric($id)) {
      $this->id = $id;
      $this->_getInfos();
    }
  }

  function exists($id = null){
    if($id == null)
      $id = $this->id;

    $result = $this->SQL("SELECT count(*) FROM webfinance_clients WHERE id_client=$id");
    list($exists) = mysql_fetch_array($result);
    return $exists;
  }
}
