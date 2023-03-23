<?php

use Phinx\Migration\AbstractMigration;

class AddClientConfigurationFieldsTwoFaImport extends AbstractMigration
{
    public function change()
    {
        $this->table('clients')
            ->addColumn('is_two_factor_auth_enforced', 'boolean', ['default' => true])
            ->addColumn('is_background_import_active', 'boolean', ['default' => false])
            ->update();
    }
}
