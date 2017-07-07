<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
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
