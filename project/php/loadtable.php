<?php
if(isset($_POST['margins']) && $_POST['margins'] == "false") {
    if(isset($_POST['sold']) 
        && isset($_POST['realty-type']) 
    ) {
        $realtor_id = (int)require_once("check_auth.php");

        require_once ("DataBase.php");
        $db = new Database();
        $margins = $db->load_default_margins();
        $realty_filter = array(
            'min_money' => $margins['min_money'], 
            'max_money' => $margins['max_money'], 
            'is_sold' => $_POST['sold'],
            'min_area' => $margins['min_area'], 
            'max_area' => $margins['max_area'], 
            'realty_type' => $_POST['realty-type'], 
            'min_date' => $margins['min_age'],
            'max_date' => $margins['max_age'],
            'district' => isset($_POST['district']) ? $_POST['district'] : 'all',
            'is_new' => false
        );
        $material_array = null;
        $table_info_arr = $db->load_realty_table($realtor_id, $realty_filter, $material_array, 'all');
        exit(json_encode(array(
            'table' => $table_info_arr,
            'margins' => $margins
        ), JSON_UNESCAPED_UNICODE));
    }
} elseif($_POST['margins'] == "true") {
    if(isset($_POST['price-min']) 
        && isset($_POST['price-max']) 
        && isset($_POST['sold']) 
        && isset($_POST['area-min']) 
        && isset($_POST['area-max']) 
        && isset($_POST['realty-type']) 
        && isset($_POST['date-min']) 
        && isset($_POST['date-max']) 
        ) {
        $realtor_id = require_once("check_auth.php");

        require_once ("DataBase.php");

        $material_array = (isset($_POST['material'])) ? $_POST['material'] : array();
        $db = new Database();
        $table_info_arr = $db->load_realty_table($realtor_id, array(
            'min_money' => $_POST['price-min'], 
            'max_money' => $_POST['price-max'], 
            'is_sold' => $_POST['sold'],
            'min_area' => $_POST['area-min'], 
            'max_area' => $_POST['area-max'], 
            'realty_type' => $_POST['realty-type'], 
            'min_date' => $_POST['date-min'],
            'max_date' => $_POST['date-max'],
            'district' => $_POST['district'],
            'is_new' => $_POST['is_new']
        ), $material_array, 'all');
        return json_encode(array(
            'table' => $table_info_arr
            )
        );
    }
}
?>