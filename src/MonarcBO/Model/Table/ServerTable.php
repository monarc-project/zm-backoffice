<?php
namespace MonarcBO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

class ServerTable extends AbstractEntityTable {
    public function __construct(\MonarcCore\Model\Db $dbService) {
        parent::__construct($dbService, '\MonarcBO\Model\Entity\Server');
    }

}