<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mimsms {

    public $api_key = "";
    public $type = "";
    public $senderId = "";
    //public $url = "https://esms.mimsms.com/smsapi";
    public $url = "https://isms.mimsms.com/smsapi";

    function __construct() {

        $ci = & get_instance();
        $school_id = '';
        if ($ci->session->userdata('school_id')) {
            $school_id = $ci->session->userdata('school_id');
        } else {
            $school_id = $ci->input->post('school_id');
        }

        $ci->db->select('S.*');
        $ci->db->from('sms_settings AS S');
        $ci->db->where('S.school_id', $school_id);
        $setting = $ci->db->get()->row();

        $this->api_key = $setting->mim_api_key;
        $this->type = $setting->mim_type;
        $this->senderId = $setting->mim_sender_id;
    }

    function sendSms($mobile, $message) {
        $url = $this->url;
        $data = [
            "api_key" => $this->api_key,
            "type" => $this->type,
            "contacts" => $mobile,
            "senderid" => $this->senderId,
            "msg" => $message
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}

?>