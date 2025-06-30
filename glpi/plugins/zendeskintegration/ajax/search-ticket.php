<?php
// Inclui as dependências do GLPI
include('../../../inc/includes.php');
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Verifica se é uma requisição AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    die('Acesso negado.');
}
// Verifica se o usuário está logado
Session::checkLoginUser();

// Processa a ação
if (isset($_POST['action']) && $_POST['action'] === 'process_ajax') {
    try {
        // Valida os dados recebidos
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $zendesk_id = $_POST['zendesk_id'] ?? '';

        if (empty($name) || empty($type)) {
            echo json_encode([
                'message' => 'Todos os campos são obrigatório',
                'status'  => 422,
            ]);
            return false;
//            throw new Exception('O campo Nome é obrigatório.');
        }

        // Cria o cliente Guzzle
        $client = new Client([
            'base_uri' => 'http://192.168.30.121:3000/',
            'timeout'  => 10.0,
        ]);

        // Construir o parâmetro query dinamicamente
        $query = [];
        if ($type === 'name') {
            $query = ['requester.name' => $name];
        } elseif ($type === 'email') {
            $query = ['requester.email' => $name];
        }

        // Dados para a requisição POST
        $postData = [
            'type' => $type,
            'name' => $name,
            'requester' => [$type => $name] // Ajustar requester no body, se necessário
        ];

        // Faz a requisição POST
        $guzzleResponse = $client->request('GET', 'tickets', [
            'query' => $query,
            'json' => $postData,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => 'e473f24d4325d51a933cc18105bcf311cc10d10c3db12204467b1bfe9df27f7e'
            ]
        ]);
        // Obtém a resposta
        $statusCode = $guzzleResponse->getStatusCode();
        $responseBody = json_decode($guzzleResponse->getBody()->getContents(), true);

        // Verifica se a requisição foi bem-sucedida
        if ($statusCode === 200 || $statusCode === 201) {
            // Salva no banco de dados do GLPI (opcional)
            $superasset = new PluginZendeskintegrationSuperasset();
            $input = [
                'name' => $name,
                'zendesk_id' => $zendesk_id,
                'date_creation' => date('Y-m-d H:i:s')
            ];
//        ?requester.email=wapn@det.ufc.br
            //germanovb@gmail.com

            $response['success'] = true;
            $response['status'] = "200";
            $response['data'] = $responseBody; // Inclui a resposta do endpoint
        } else {
            throw new Exception('Falha ao criar ticket. Status: ' . $statusCode);
        }
    } catch (RequestException $e) {
        // Captura erros de rede ou da API
        $response['message'] = 'Erro na requisição: ' . $e->getMessage();
        if ($e->hasResponse()) {
            $response['message'] .= ' - ' . $e->getResponse()->getBody()->getContents();
        }
    } catch (Exception $e) {
        // Captura outros erros
        $response['message'] = $e->getMessage();
    }

    // Retorna a resposta em JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'ticket_id') {
    try {
        $ticket_id = $_POST['id'] ?? '';

        if (empty($ticket_id)) {
            echo json_encode([
                'message' => 'Todos os campos são obrigatório',
                'status'  => 422,
            ]);
            return false;
//            throw new Exception('O campo Nome é obrigatório.');
        }
        // Cria o cliente Guzzle
        $client = new Client([
            'base_uri' => 'http://192.168.30.121:3000/',
            'timeout'  => 10.0,
        ]);
        $guzzleResponse = $client->request('GET', 'tickets/'.$ticket_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => 'e473f24d4325d51a933cc18105bcf311cc10d10c3db12204467b1bfe9df27f7e'
            ]
        ]);
        // Obtém a resposta
        $statusCode = $guzzleResponse->getStatusCode();
        $responseBody = json_decode($guzzleResponse->getBody()->getContents(), true);

        if ($statusCode === 200 || $statusCode === 201) {
            $response['success'] = true;
            $response['status'] = "200";
            $response['data'] = $responseBody; // Inclui a resposta do endpoint
        } else {
            throw new Exception('Falha ao criar ticket. Status: ' . $statusCode);
        }
    }catch (RequestException $e) {
        $response['message'] = $e->getMessage();
    }
// Retorna a resposta em JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
