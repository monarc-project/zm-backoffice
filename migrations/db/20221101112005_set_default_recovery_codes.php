<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SetDefaultRecoveryCodes extends AbstractMigration
{
    public function change()
    {
        // Fix nullable recovery_codes of users.
        $this->execute('update users set recovery_codes = "' . serialize([]) . '" where recovery_codes IS NULL');

        $this->execute('ALTER TABLE clients DROP COLUMN model_id, DROP FOREIGN KEY clients_ibfk_2;');
        $this->execute('ALTER TABLE clients DROP INDEX logo_id;');
    }
}
