<?php


use Search;
use Html;

include ('../../../inc/includes.php');

Html::header(
    PluginZendeskintegrationSuperasset::getTypeName(),
    $_SERVER['PHP_SELF'],
    "plugins",
    PluginZendeskintegrationSuperasset::class,
    "superasset"
);
Search::show(PluginZendeskintegrationSuperasset::class);
Html::footer();