<?php
// Inclui as dependências do GLPI
include('../../../inc/includes.php');

// Verifica se é uma requisição AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    die('Acesso negado.');
}
// Verifica se o usuário está logado
Session::checkLoginUser();

// Processa a ação
if (isset($_POST['action']) && $_POST['action'] === 'process_ajax') {
    var_dump($_POST); die;


}
