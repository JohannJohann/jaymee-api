<?php

namespace App\Service;


class FirebasePushService {

    public function sendPushNotification($informations)
    {   
        $SERVER_KEY = "AAAAzUB71Tw:APA91bHlR6xZDwzrG-QlzCPFMG6yKaqOI7YvMCpBaqsF-ORIiRF-1D9J_gVGFlIDR2Wgz6xGi17FslhQ-FsZxKZt0kagoEd_67vOJle_2It-nkBRhN5otxr3BSOreC_bDBiSNTV9h4sC";
        $URL = 'https://fcm.googleapis.com/fcm/send';

        $target = $informations['target'];
        $code = $informations['code'];
        if(!is_null($target) && !is_null($code)){
            // On construit le contenu de la notification selon le code correspondant
            switch($code){
                // Partage de nouvelle photo
                case 1 : 
                    $data = array(
                        "notification"=>array(
                            "title"=>"Suspense suspense ...",
                            "body"=>$informations['source']." a partagé une nouvelle photo",
                            "click_action"=>"FCM_PLUGIN_ACTIVITY",
                            "sound"=> "default",
                            "icon"=> "fcm_push_icon",
                            'color' => '#bf00db',
                        ),
                        "data"=>array(
                            "code"=>1,
                        ),
                        "to"=>$target,
                        "priority"=>"high"
                    );
                    break;

                default : 

            }

            // Envoi de la notification
            if(!is_null($data)){
                 $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_URL => $URL,
                    CURLOPT_USERAGENT => '',
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_HTTPHEADER => array(       
                        'Authorization: key='.$SERVER_KEY,                                                                   
                        'Content-Type: application/json'                                                                                          
                    ),
                    CURLOPT_POSTFIELDS=> json_encode($data)   
                ));

                $resp = curl_exec($curl);  
                curl_close($curl);
                return $resp;   
            } else {
            }
        }
    }

}