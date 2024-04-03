<?php

require ('includes/connect.php');

$query1 = "SELECT * FROM games WHERE category_id = 1";
$statement_action = $db->prepare($query1);
$statement_action->execute();

$query2 = "SELECT * FROM games WHERE category_id = 2";
$statement_adventure = $db->prepare($query2);
$statement_adventure->execute();

$query3 = "SELECT * FROM games WHERE category_id = 3";
$statement_sports = $db->prepare($query3);
$statement_sports->execute();
