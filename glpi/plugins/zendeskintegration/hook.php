<?php
/**
 * Install hook
 *
 * @return boolean
 */
function plugin_zendeskintegration_install() {
   //do some stuff like instantiating databases, default values, ...
   return true;
}

/**
 * Uninstall hook
 *
 * @return boolean
 */
function plugin_zendeskintegration_uninstall() {
   //to some stuff, like removing tables, generated files, ...
   return true;
}

/**
 * Add menu entries for the plugin
 */
global $PLUGIN_HOOKS;
$PLUGIN_HOOKS['menu_toadd']['zendeskintegration'] = [
    'plugins' => [
        [
            'name' => __('Super Assets', 'zendeskintegration'),
            'link' => '/plugins/zendeskintegration/front/superasset.form.php',
            'class' => 'PluginZendeskintegrationSuperasset',
            'icon' => 'fas fa-cubes', // Ícone do FontAwesome
            'rights' => [
                'plugin_zendeskintegration_superasset' => READ // Permissões necessárias
            ]
        ]
    ]
];