<?php

/**
 * Created by PhpStorm.
 * User: Юлия
 * Date: 14.12.2016
 * Time: 1:17
 */

class DataBase
{
    private $db;

    public function __construct()
    {
        $this->db = pg_connect("host=localhost port=5432 dbname=realty_db user=realtor password=realtor options='--client_encoding=UTF8'");
        if(!$this->db){
            print "error";
        }
    }

    public function insert_realty($realtor_id, $address_info, $realty_info, $proprietor_info) { // добавляет новую недвижимость в БД

        $address_query = pg_prepare($this->db, "insert_address",
            'INSERT INTO address (flat_number, building_number, corpus_number, street, district) VALUES ($1,$2,$3,$4,$5) RETURNING address_id;');
        $address_query = pg_execute($this->db, "insert_address", array($address_info['flat_number'],$address_info['building_number'],
            $address_info['corpus_number'],$address_info['street'],$address_info['district']));

        $personal_data_query = pg_prepare($this->db, "insert_personal_data",
            'INSERT INTO personal_data (surname, name, father_name, phone, email, date_of_birth) VALUES ($1,$2,$3,$4,$5,$6) RETURNING personal_data_id;');
        $personal_data_query = pg_execute($this->db, "insert_personal_data", array($proprietor_info['surname'],$proprietor_info['name'],
            $proprietor_info['father_name'],$proprietor_info['phone'],$proprietor_info['email'],$proprietor_info['date_of_birth']));

        $proprietor_query = pg_prepare($this->db, "insert_proprietor",
            'INSERT INTO proprietor (propreitor_id) VALUES ($1)');
        $proprietor_query = pg_execute($this->db, "insert_proprietor", array($personal_data_query));

        $realty_proprietor_query = pg_prepare($this->db, "insert_realty_proprietor",
            'INSERT INTO realty_proprietor (propreitor_id, realty_id) VALUES ($1,$2)');
        $realty_proprietor_query = pg_execute($this->db, "insert_realty_proprietor", array($personal_data_query,$address_query));

        $realty_query = pg_prepare($this->db, "insert_realty",
            'INSERT INTO realty (address_id, realtor_id, money, hide, description, area, floor, number_of_floors, number_of_rooms, with_decoration, 
            subway, time_from_subway, realty_type, is_new, has_electricity, has_gas, building_age, apartment_complex, building_material, around_area)
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20);');
        $realty_query = pg_execute($this->db, "insert_realty", array($address_query, $realtor_id, $realty_info['money'],$realty_info['hide'],
            $realty_info['description'],$realty_info['area'],$realty_info['floor'],$realty_info['number_of_floors'],$realty_info['number_of_rooms'],
            $realty_info['with_decoration'],$realty_info['subway'],$realty_info['time_from_subway'],$realty_info['realty_type'],$realty_info['is_new'],
            $realty_info['has_electricity'],$realty_info['has_gas'],$realty_info['building_age'],$realty_info['apartment_complex'],
            $realty_info['building_material'],$realty_info['around_area']));

        if ($address_query == false or $personal_data_query == false or $proprietor_query == false or $realty_proprietor_query == false or $realty_query == false)
        {
            echo "insert_realty error";
        }
    }

    public function insert_firm($director_id, $firm_info, $address_info) {
        $address_query = null;
        if ($address_info != null) {
            $address_query = pg_prepare($this->db, "insert_address",
                'INSERT INTO address (flat_number, building_number, corpus_number, street, district) VALUES ($1,$2,$3,$4,$5) RETURNING address_id;');
            $address_query = pg_execute($this->db, "insert_address", array($address_info['flat_number'], $address_info['building_number'],
                $address_info['corpus_number'], $address_info['street'], $address_info['district']));
        }
        $firm_query = pg_prepare($this->db, "insert_firm",
            'INSERT INTO firm (director_id, name, address_id, phone, email) VALUES ($1,$2,$3,$4,$5);');
        $firm_query = pg_execute($this->db, "insert_firm", array($director_id,$firm_info['name'],
            $address_query,$firm_info['phone'],$firm_info['email']));

        if ($address_query == false or $firm_query == false)
        {
            echo "insert_firm error";
        }
    }

    public function load_authorization_info($login) {
        $authorization = pg_prepare($this->db, "authorization", 'SELECT (realtor_id, password, creation_time) FROM realtor WHERE login = $1');
        $authorization = pg_execute($this->db, "authorization", array($login));
        $result = pg_fetch_assoc($authorization);
        if ($authorization == false)
        {
            echo "load_authorization_info error";
            echo pg_last_error();
        }
        return $result;
    }

    public function load_default_margins() {
        $load_margins = pg_prepare($this->db, "margins", 'SELECT min(area) AS min_area, max(area) AS max_area, min(money) AS min_money, max(money) AS max_money, min(building_age) AS min_age, max(building_age) AS max_age FROM realty');
        $load_margins = pg_execute($this->db, "margins", array());
        if ($load_margins == false)
        {
            echo "load_authorization_info error";
            echo pg_last_error();
        }
        $load_margins = pg_fetch_assoc($load_margins);
        return $load_margins;
    }

    public function load_realty_table($realtor_id, $arr, $material, $firm_list)
    { // сделала эту функцию работающей для вкладки "фирма"
        $firm_query = "SELECT firm_id FROM realtor WHERE realtor_id =".$realtor_id.";";
        $firm_id = pg_query($this->db, $firm_query);
        $firm_id = pg_fetch_array($firm_id)[0];

        if ($firm_list == null) {
            $firm_string = "(hide = false OR firm_id = ".$firm_id.")";
        }// если null, то грузятся вообще все квартиры, в том числе и фирмы
        if ($firm_list == 'all') {
            $firm_string = "firm_id = ".$firm_id;
        } // еслии 'all', то грузятся _только_ квартиры сотрудников, но при этом все
        if (is_array($firm_list) && count($firm_list) != 0) {
            $firm_string = "(";
            for ($x = 0; $x < count($firm_list)-1; $x++) {
                $firm_string.="realty.realtor_id =".$firm_list[$x]." OR ";
            }
            $firm_string.="realty.realtor_id =".$firm_list[count($firm_list)-1].")";
        }

        $query = "SELECT realty.address_id AS addressId, realty.realtor_id AS realtorId, money, offer_creation_time, views, area, district, realty_type, floor, number_of_floors, number_of_rooms, subway".
            " FROM realty JOIN address on realty.address_id = address.address_id JOIN realtor on realty.realtor_id = realtor.realtor_id WHERE money<= ".
            $arr['max_money']." AND money>=".$arr['min_money']." AND is_removed = false AND is_sold = ".$arr['is_sold']." AND area >= ".$arr['min_area'].
            " AND area <= ".$arr['max_area']." AND realty_type = '".$arr['realty_type']."' AND building_age >= '".$arr['min_date'].
            "' AND building_age <= '".$arr['max_date']."' AND ".$firm_string;

        if (is_array($material) || count($material) != 0) {
            $query.= " AND (";
            for ($x = 0; $x < count($material)-1; $x++) {
                $query.="building_material =".$material[$x]." OR ";
            }
            $query.="building_material =".$material[count($material)-1].")";
        }
        if ($arr['district']!='all') {
            $query.=" AND district =".$arr['district'];
        }
        if ($arr['is_new']) {
            $query.=" AND is_new =".$arr['is_new'];
        }
        $query.=";";

        $result = pg_query($this->db, $query);
        if (!$result) {
            echo "load_realty_table error";
        } else {
            $result = pg_fetch_all($result);
        }
        return $result;
    }

    public function get_proprietor_id_by_realty_id($realty_id) {
        // $query = "SELECT proprietor_id FROM realty_proprietor WHERE "
    }
    public function load_full_realty_info($realty_id) {

        return $result;
    }

    public function load_user_info() {
        return $result;
    }

    public function load_proprietor_info () {
        return $result;
    }

    public function load_firm_info () {
        return $result;
    }

    public function mark_as_sold() {
        return $result;
    }

    public function mark_as_removed() {
        return $result;
    }

    public function mark_as_manager() {
        return $result;
    }

    public function mark_as_not_manager() {
        return $result;
    }

    public function load_employees_list() {
        return $result;
    }

    public function update_personal_info() {
        return $result;
    }

    public function update_realty_info() {
        return $result;
    }

    public function appoint_new_realtor() {

    }
    public function update_firm_info() {
        return $result;
    }

    public function add_employee() {
        return $result;
    }

    public function delete_employee() {
        return $result;
    }

    public function delete_firm() {
        return $result;
    }

    public function delete_realty() {
        return $result;
    }

    public function add_view() {
        return $result;
    }
    
    public function update_realtor_hash($realtor_id, $hash) {
        $update_hash = pg_prepare($this->db, "update_hash", 'UPDATE realtor SET hash = $1 WHERE realtor_id = $2');
        $update_hash = pg_execute($this->db, "update_hash", array($hash, $realtor_id));
        if($update_hash == false) {
            echo "update hash error";
        }
    }
}