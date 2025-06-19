<?php
// Inclui as dependências do GLPI
include('../../../inc/includes.php');
require '../vendor/autoload.php';

use GuzzleHttp\Client;

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
        $zendesk_id = $_POST['zendesk_id'] ?? '';

        if (empty($name)) {
            throw new Exception('O campo Nome é obrigatório.');
        }

        // Cria o cliente Guzzle
        $client = new Client([
            'base_uri' => 'http://192.168.30.121:3000/',
            'timeout'  => 10.0,
        ]);

        // Dados para a requisição POST
        $postData = [
            'name' => $name,
            'zendesk_id' => $zendesk_id,
            'requester' => [
                'email' => $name
            ]
        ];

        // Faz a requisição POST
        $guzzleResponse = $client->request('GET', 'tickets', [
            'query' => ['requester.email' => $name],
            'json' => $postData,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
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
            $response['message'] = "Ticket criado com sucesso no endpoint. Super Asset salvo com ID $new_id.";
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
