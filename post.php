<?php

namespace vkApi;

class post{
    private $vk;
    private $owner;
    function __construct(vk $vk, $user = null, $group = null){
        $this->vk = $vk;
        if(!$user && !$group){
            throw new \Exception('Not found group or user');
        }
        $this->owner = array(
            'type' => $user ? 'owner_id' : 'group_id',
            'value' => $user ? $user : $group
        );
        $this->owner['value'] = (int)preg_replace('/([^\d]+)/', '', $this->owner['value']);
    }

    function post($text, $img = null){
        if($img) {
            $data = $this->load($img);
            $img = $data->response[0]->id;
        }
        $data = array(
            'message' => $text,
            'owner_id' => $this->owner['value']
        );
        if($img){
            $data['attachments'] = $img;
        }
        if($this->owner['type'] == 'group_id'){
            $data['owner_id'] = '-' . $data['owner_id'];
        }

        $data = $this->vk->get('wall.post', $data);
        if(isset($data->error)){
            throw new \Exception($data->error->error_msg);
        }
        return $data;
    }

    function load($src){
        $photo = (array)$this->getPhoto($src);
        $photo[$this->owner['type']] = $this->owner['value'];
        $data = $this->vk->get('photos.saveWallPhoto', $photo);
        return $data;
    }

    private function getPhoto($src){
        $name = __DIR__ . DIRECTORY_SEPARATOR . '1.png';
        file_put_contents($name, file_get_contents($src));
        $ch = curl_init($this->getServer());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'photo' => '@' . $name
        ));
        $response = curl_exec( $ch );
        curl_close( $ch );
        return json_decode($response);
    }

    private function getServer(){
        $data = $this->vk->get('photos.getWallUploadServer', array(
            $this->owner['type'] => $this->owner['value'],
        ));
        return $data->response->upload_url;
    }


}