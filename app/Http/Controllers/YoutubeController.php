<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YouTubeService;
use DateInterval;

class YoutubeController extends Controller
{
    //search?part=snippet&q=silverchair&maxResults=50&key=AIzaSyDyR8KgT5ft2HShINFEgAdqld2sugn-sw8
    public function index(Request $request)
    {
        //$request->session()->forget('tempoSemana');
        return view('index');
    }

    public function definirSessao(Request $request)
    {

        if (empty($request->input('tempoSemanal'))) {
            return array('success' => false);
        }
        $totalAtual = $this->converterMinutosEmSegundos($request->input('tempoSemanal'));
        $request->session()->put('tempoSemana', $totalAtual);
        return array('success' => true, 'retorno' => 'Tempo samanal definido.');
    }

    public function tempoUtilizado(Request $request)
    {
        if ($request->session()->has('tempoSemana')) {
            $tempoAtual = $request->session()->get('tempoSemana');
            return array('success' => true, 'retorno' => $tempoAtual);
        } else {
            return array('success' => false);
        }
    }

    public function remover_acento($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    
    public function buscar(Request $request, YouTubeService $youTubeService)
    {

        $dadosArray['q'] = $this->remover_acento($request->input('q'));

        $search = $youTubeService->processaTransacao($dadosArray, 'search');

        if ($search !== true) {
        }

        $retorno = json_decode($search['retorno'], true);

        foreach ($retorno['items'] as $key => $val) {

            $dadosArray['videoId'] = $val['id']['videoId'];
            $detailStats[] = $youTubeService->processaTransacao($dadosArray, 'info');
            foreach ($detailStats as $k => $v) {
                $detail         = json_decode($v['retorno'], true);
                $interval       = new DateInterval($detail['items'][0]['contentDetails']['duration']);
                $formated_stamp = $interval->h * 3600 + $interval->i * 60 + $interval->s;
                $stats[]        = array(
                    'title' => $val['snippet']['description'], 'thumb' => $val['snippet']['thumbnails']['high']['url'],
                    'duration' => $this->converterSegundosEmMinutos($formated_stamp),
                    'id' => $val['id']['videoId'],
                    'seconds' => $formated_stamp,
                    'button' => '<a href="/watch/' . $val['id']['videoId'] . '/' . $formated_stamp . '" class="btn btn-primary btn-sm">Assistir</a>'
                );
            }
        }

        return array('success' => true, 'retorno' => $stats);
    }


    function converterSegundosEmMinutos($seconds_time)
    {
        if ($seconds_time < 24 * 60 * 60) {
            return gmdate('H:i:s', $seconds_time);
        } else {
            $hours = floor($seconds_time / 3600);
            $minutes = floor(($seconds_time - $hours * 3600) / 60);
            $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));
            return "$hours:$minutes:$seconds";
        }
    }

    function converterMinutosEmSegundos($minutos)
    {
        $timeInSeconds = $minutos * 60;
        return $timeInSeconds;
    }

    public function assistir($id, $segundosAssistidos, Request $request)
    {
        // Recupero tempo semanal, setado na busca.
        $tempoSemanal = $request->session()->get('tempoSemana');

        // Verifico se ainda tem saldo para assistir algo
        $permiteAssistir = $this->calculoSemanal($tempoSemanal, $segundosAssistidos);

        $saldoAtual = $this->converterSegundosEmMinutos($tempoSemanal);
        $tamanhOVideo = $this->converterSegundosEmMinutos($segundosAssistidos);
        if (!$permiteAssistir) {
            $mensagem = "Sem saldo semanal para assistir.";

            return view('watch', compact('mensagem', 'saldoAtual', 'tamanhOVideo'));
        }

        // Após assistir, seto o novo tempo disponível e atualizo a session
        $novo_tempo = $tempoSemanal - $segundosAssistidos;
        $request->session()->put('tempoSemana', $novo_tempo);

        return view('watch', compact('id', 'saldoAtual', 'tamanhOVideo'));
    }

    function calculoSemanal($tempoAtual, $tempoDescrecido)
    {
        if ($tempoAtual < $tempoDescrecido) {
            return false;
        }
        return true;
    }
}
