<?php
if (!defined('GLPI_ROOT')) {
    die("Sorry, you can't access this file directly");
}

class PluginZendeskintegrationMenu extends CommonDBTM {
    static $rightname = 'config';

    static function getMenuName() {
        return __('Zendesk Integration', 'zendeskintegration');
    }

    static function getMenuContent() {
        $menu = [];
        $menu['title'] = self::getMenuName();
        $menu['page']  = Plugin::getWebDir('zendeskintegration', false).'/front/superasset.form.php';
        $menu['icon']  = 'fas fa-ticket-alt';
        return $menu;
    }
}