<?php

define('PLUGIN_ZENDESKINTEGRATION_VERSION', '1.0.0');

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */
function plugin_init_zendeskintegration() {
   global $PLUGIN_HOOKS;
   $PLUGIN_HOOKS['csrf_compliant']['zendeskintegration'] = true;
   $PLUGIN_HOOKS['menu_toadd']['zendeskintegration'] = [
        // insert into 'plugin menu'
        'plugins' => PluginZendeskintegrationSuperasset::class
    ];

   // Adicionar ao menu
   Plugin::registerClass('PluginZendeskintegrationSuperasset', [
       'addtabon' => [
           'Computer', 'Ticket'
       ] // Exemplo: adiciona abas em outros itens
   ]);

    // Adicionar permissões
    $PLUGIN_HOOKS['config_page']['zendeskintegration'] = 'front/config.form.php'; // Opcional: página de configuração
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_zendeskintegration() {
   return [
       'name'          => __('Zendesk Integration', 'zendeskintegration'),
      'version'        => PLUGIN_ZENDESKINTEGRATION_VERSION,
      'author'         => 'Junior Oliveira>',
      'license'        => 'GLPv3',
      'homepage'       => 'http://perdu.com',
      'requirements'   => [
         'glpi'   => [
            'min' => '10.0',
            'max' => '11.0'
         ]
      ]
   ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_check_prerequisites() {
   //do what the checks you want
    if (version_compare(GLPI_VERSION, '10.0', 'lt') || version_compare(GLPI_VERSION, '11.0', 'ge')) {
        echo "This plugin requires GLPI >= 10.0 and < 11.0";
        return false;
    }
   return true;
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 */
function plugin_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo "Installed, but not configured";
   }
   return false;
}

/**
 * Optional: defines plugin options.
 *
 * @return array
 */
function plugin_zendeskintegration_options() {
   return [
      Plugin::OPTION_AUTOINSTALL_DISABLED => true,
   ];
}