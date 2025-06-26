<?php
// Carrega as dependências do GLPI
include('../../../inc/includes.php');

// Verifica se o usuário está logado
Session::checkLoginUser();

// Usa a classe correta com o namespace
use PluginZendeskintegrationSuperasset;

$superasset = new PluginZendeskintegrationSuperasset();

// Exibe o cabeçalho
Html::header(
    PluginZendeskintegrationSuperasset::getTypeName(),
    $_SERVER['PHP_SELF'],
    "plugins",
    PluginZendeskintegrationSuperasset::class,
    "superasset"
);

// Exibe o formulário
$superasset->showForm(23,['id' => 12]);
Html::footer();