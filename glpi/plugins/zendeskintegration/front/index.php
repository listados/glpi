<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../../inc/includes.php');
use Glpi\Application\View\TemplateRenderer;
// Verifica permissões
Session::checkRight('config', READ);

// Renderização manual (sem Twig)

// Dados de exemplo (substitua pela sua lógica real)
$data = [
    'title' => __('Integração Zendesk', 'zendeskintegration'),
    'tickets' => [
        ['id' => 1, 'subject' => 'Problema com login'],
        ['id' => 2, 'subject' => 'Solicitação de acesso']
    ]
];
Html::header(__('Zendesk Integration', 'zendeskintegration'), $_SERVER['PHP_SELF'], 'tools');

echo "<div class='center'>";

echo '<div class="my-3 p-3 bg-body rounded shadow-sm"><form>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Digite o titulo do ticker</label>
    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
   
  </div>
 
  <button type="submit" class="btn btn-primary">Buscar Ticket</button>
</form></div>';
echo "</div>";

Html::footer();


// Inclui o footer do GLPI
// Html::footer();