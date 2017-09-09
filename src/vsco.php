<?php

include_once dirname(__FILE__) . '/agent.php';

class VSCO extends Agent {

    public $auth_token = "";
    public $expire_time = 0;

    public $username= "";
    public $password = "";

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;

        $this->login($username, $password);
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        $result = parent::post('oauth/passwordgrant', [
            "password"      =>      $password,   
            "username"      =>      $username,      
            "phone"         =>      "",      
            "app_id"        =>      "A7ED1B3A-9EA2-4FAF-BD19-80401B10E510",        
            "grant_type"    =>      "password",
        ], "AaB03x");

        $this->auth_token = $result->access_token;
        $this->expire_time = strtotime("+{$result->expires_in} seconds"); //expires_in is how many seconds until the token expires

        return $result;
    }

    public function searchUsers($query, $page = 0, $size = 30) {
        return parent::get('search/grids?page=' . $page . '&query=' . $query . '&size='. $size, $this->auth_token)->results;
    }

    public function getSite($siteId) {
        return parent::get('sites/' . $siteId, $this->auth_token);
    }

    public function getMedia($siteId, $page = 1, $size = 30) {
        return parent::get('medias?page=' . $page . '&site_id=' . $siteId . '&size='. $size, $this->auth_token)->media;
    }

    public function favoriteMedia($mediaId, $siteId) {
        return parent::post('collections/favorite/media', [
            "media_ids" => [
                "type" => "grid_image",
                "media_id" => $mediaId
            ],
            "site_id" => $siteId
        ], null, $this->auth_token);
    }
}
