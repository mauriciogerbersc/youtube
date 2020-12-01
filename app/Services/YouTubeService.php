<?php

namespace App\Services;

class YouTubeService {

    private $url_api;
    private $api_key;

    public function __construct(){
       $this->url_api = "https://www.googleapis.com/youtube/v3/";
       $this->api_key = "AIzaSyAmxqHzf2Nz0xJBwZ7DbQox6y1rTzAYh-k";
    }


    public function processaTransacao($dadosArray, $tipoTransacao){
         
        switch($tipoTransacao){
            case "search": 
                return $this->connectApi($dadosArray, "search");
                break;
            case "info":
                return $this->connectApi($dadosArray, "info");
                break;
        }

    }

    public function connectApi($dadosArray, $tipoTransacao){
       // search?part=snippet&q=silverchair&maxResults=50&key=AIzaSyDyR8KgT5ft2HShINFEgAdqld2sugn-sw8
    
        if($tipoTransacao=='search'){
            $busca = urlencode($dadosArray['q']);
            $url_transacao = 'search?part=snippet&q='.$busca.'&maxResults=2';
        }elseif($tipoTransacao=='info'){
            $url_transacao = 'videos?part=contentDetails&id='.$dadosArray['videoId'];
        }
        // Url final de transação
        $url_api = $this->url_api.$url_transacao.'&key='.$this->api_key;
  
        $curl = curl_init();

        try {
            curl_setopt($curl, CURLOPT_URL,$url_api);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    
            $retorno = curl_exec($curl);
        
            $infoRequisicao = curl_getinfo($curl);
    
            curl_close($curl);
    
            if($infoRequisicao['http_code']!=200){
                return array('success' => false, 'mensagem' => "Ocorreu um erro [STATUS {$infoRequisicao['http_code']}] ao executar esta ação. Contate o suporte técnico.");
            }

            return array('success' => true, 'retorno' => $retorno);

         
        }catch(Exception $ex) {
            return array('success' => false, 'mensagem' => 'Ocorreu o segionte erro (EXCEPTION): ' . $ex->getMessage() . '.<br>Favor contatar o suporte técnico.');
        }
    }

}