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

include("inc/main.php");
$title = "Session";
include("top.php");

if ($_GET['delete']) {
  unset($_SESSION[$_GET['delete']]);
  header("Location: session.php");
}

print "<pre>";

?>
<form action="session.php" method="post">'

<?php
foreach ($_SESSION as $n=>$v) {
  print "<a href=\"?delete=$n\"><input type=\"text\" name=\"$n\" value=\"$n\"/></a> : ";
  print_r($_SESSION[$n]);
  print "\n";
}
print "</pre>";

$Revision = '$Revision: 531 $';
include("bottom.php");

?>
