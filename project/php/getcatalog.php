<?php
/**
 * Created by PhpStorm.
 * User: Юлия
 * Date: 13.12.2016
 * Time: 22:35
 */

require_once ("DataBase.php");

$db = new DataBase();

echo json_encode($db->load_pd_row(3),JSON_UNESCAPED_UNICODE);
