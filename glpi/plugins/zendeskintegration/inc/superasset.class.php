<?php

use Glpi\Application\View\TemplateRenderer;


class PluginZendeskintegrationSuperasset extends CommonDBTM {

    // Define direitos
    public function getRights($interface = 'central') {
        return [
            READ => __('Read Super Assets', 'zendeskintegration'),
            CREATE => __('Create Super Assets', 'zendeskintegration'),
            UPDATE => __('Update Super Assets', 'zendeskintegration'),
            DELETE => __('Delete Super Assets', 'zendeskintegration')
        ];
    }
    static function getTypeName($nb = 0) {
        return __('Integração Zendesk', 'zendeskintegration');
    }

    static function getMenuName($nb = 0)
    {
        // call class label
        return self::getTypeName($nb);
    }

   // URL do formulário
    public static function getFormURL($full = true) {
        global $CFG_GLPI;
        $dir = ($full) ? $CFG_GLPI['root_doc'] . "/" : '';
        return $dir . "plugins/zendeskintegration/front/superasset.form.php";
    }

    // Exibe o formulário
    public function showForm($ID, $options = []) {

        $arrayTwig = [
            'template',
            'illustrates',
            'basics'
        ];
        TemplateRenderer::getInstance()->display('@zendeskintegration/superasset.form.html.twig', [
            'item'   => $this,
            'params' => $options,
            'arrayTwig' => $arrayTwig
        ]);

    }

    // Redireciona para a lista
    public function redirectToList() {
        Html::redirect($CFG_GLPI['root_doc'] . "/plugins/zendeskintegration/front/superasset.php");
    }

/**
     * Define additionnal links used in breacrumbs and sub-menu
     *
     * A default implementation is provided by CommonDBTM
     */
    static function getMenuContent()
    {
        return [
            'title' => __('Zendesk Ticket', 'zendeskintegration'),
            'page' => '/plugins/zendeskintegration/front/superasset.form.php',
            'icon' => 'fas fa-cubes'
        ];
    }
}
