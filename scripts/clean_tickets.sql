-- Limpeza de todos os tickets e dados relacionados, preservando
-- usuarios, perfis, SLA/OLA (configuracao), calendarios, notificacoes,
-- SMTP/IMAP e demais configuracoes do GLPI.
START TRANSACTION;

-- 1) Tabelas de relacionamento direto (coluna tickets_id)
DELETE FROM glpi_changes_tickets;
DELETE FROM glpi_groups_tickets;
DELETE FROM glpi_items_tickets;
DELETE FROM glpi_olalevels_tickets;
DELETE FROM glpi_problems_tickets;
DELETE FROM glpi_projecttasks_tickets;
DELETE FROM glpi_slalevels_tickets;
DELETE FROM glpi_suppliers_tickets;
DELETE FROM glpi_ticketcosts;
DELETE FROM glpi_tickets_contracts;
DELETE FROM glpi_tickets_users;
DELETE FROM glpi_ticketsatisfactions;
DELETE FROM glpi_tickettasks;
DELETE FROM glpi_ticketvalidations;
DELETE FROM glpi_tickets_tickets;

-- 2) Tabelas polimorficas (itemtype='Ticket')
DELETE FROM glpi_documents_items WHERE itemtype = 'Ticket';
DELETE FROM glpi_itilfollowups WHERE itemtype = 'Ticket';
DELETE FROM glpi_logs WHERE itemtype = 'Ticket';
DELETE FROM glpi_itilsolutions WHERE itemtype = 'Ticket';
DELETE FROM glpi_queuednotifications WHERE itemtype = 'Ticket';
DELETE FROM glpi_plugin_fields_ticketinformaesadicionaisdechamados WHERE itemtype = 'Ticket';
DELETE FROM glpi_pendingreasons_items WHERE itemtype = 'Ticket';

-- 3) Desvincula (nao apaga) documentos que tinham link legado direto a um ticket
UPDATE glpi_documents SET tickets_id = 0 WHERE tickets_id > 0;

-- 4) Por fim, os tickets propriamente ditos
DELETE FROM glpi_tickets;

-- 5) Reinicia o auto_increment para IDs limpos em producao
ALTER TABLE glpi_tickets AUTO_INCREMENT = 1;

COMMIT;
