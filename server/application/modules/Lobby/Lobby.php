<?php

class Lobby
{
    private DB $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function selectTeam($id, $teamId)
    {
        return $this->db->addPlayerToTeam($id, $teamId);

    }

    public function getTeamsInfo()
    {
        return  $this->db->getTeamsInfo();
        

    }
    public function getSkins(){
        $skins = $this->db->getSkinsInLobby();
        return [
            $skins->id=>[
                'text' =>$skins->id->text,
                'image' => $skins->id->image
            ]
            ];

    }
    public function setSkin($id, $skinId){
        return $this->db->setSkinInLobby($id, $skinId);
    }



}