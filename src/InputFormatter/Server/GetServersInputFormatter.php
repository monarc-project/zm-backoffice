<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\InputFormatter\Server;

use Monarc\BackOffice\Entity\Server;
use Monarc\Core\InputFormatter\AbstractInputFormatter;

class GetServersInputFormatter extends AbstractInputFormatter
{
    protected const DEFAULT_LIMIT = 0;

    protected static array $allowedFilterFields = [
        'status' => [
            'default' => Server::STATUS_ACTIVE,
            'type' => 'int',
        ],
    ];

    protected static array $ignoredFilterFieldValues = ['status' => 'all'];
}
