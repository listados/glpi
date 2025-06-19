<?php

use Glpi\Application\View\TemplateRenderer;


class PluginZendeskintegrationSuperasset extends CommonDBTM {
    static function getTypeName($nb = 0) {
        return 'Integração com Zendesk';
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
        $title  = self::getMenuName(Session::getPluralNumber());
        $search = self::getSearchURL(false);
        $form   = self::getFormURL(false);

        // define base menu
        $menu = [
            'title' => __("My plugin", 'zendeskintegration'),
            'page'  => $search,

            // define sub-options
            // we may have multiple pages under the "Plugin > My type" menu
            'options' => [
                'superasset' => [
                    'title' => $title,
                    'page'  => $search,

                    //define standard icons in sub-menu
                    'links' => [
                        'search' => $search,
                        'add'    => $form
                    ]
                ]
            ]
        ];

        return $menu;
    }
}