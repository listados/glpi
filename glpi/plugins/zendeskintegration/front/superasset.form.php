<?php
// Carrega as dependências do GLPI
include('../../../inc/includes.php');

// Verifica se o usuário está logado
Session::checkLoginUser();

// Usa a classe correta com o namespace
use PluginZendeskintegrationSuperasset;

$superasset = new PluginZendeskintegrationSuperasset();

if (isset($_POST["add"])) {
    $newID = $superasset->add($_POST);
    if ($_SESSION['glpibackcreated']) {
        Html::redirect(PluginZendeskintegrationSuperasset::getFormURL() . "?id=" . $newID);
    }
    Html::back();
} elseif (isset($_POST["delete"])) {
    $superasset->delete($_POST);
    $superasset->redirectToList();
} elseif (isset($_POST["restore"])) {
    $superasset->restore($_POST);
    $superasset->redirectToList();
} elseif (isset($_POST["purge"])) {
    $superasset->delete($_POST, 1);
    $superasset->redirectToList();
} elseif (isset($_POST["update"])) {
    $superasset->update($_POST);
    Html::back();
} else {
    // Define o ID, se fornecido
    $ID = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
}