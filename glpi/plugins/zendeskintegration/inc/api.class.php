<?php
class PluginZendeskintegrationApi {
    static function getTickets() {
        // Implemente a conexão com a API Zendesk aqui
        return [
            ['id' => 123, 'subject' => 'Problema com login', 'status' => 'open'],
            ['id' => 456, 'subject' => 'Pedido de suporte', 'status' => 'pending']
        ];
    }
}