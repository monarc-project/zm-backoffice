<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2018 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */
namespace MonarcBO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

/**
 * Class ServerTable
 * @package MonarcBO\Model\Table
 */
class ServerTable extends AbstractEntityTable {
    /**
     * ServerTable constructor.
     * @param \MonarcCore\Model\Db $dbService
     */
    public function __construct(\MonarcCore\Model\Db $dbService) {
        parent::__construct($dbService, '\MonarcBO\Model\Entity\Server');
    }
}
