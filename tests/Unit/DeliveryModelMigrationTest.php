<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit;

use Phinx\Db\Adapter\SQLiteAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

/** @coversNothing */
class DeliveryModelMigrationTest extends TestCase
{
    public function testDroppingUnusedColumnPreservesTemplateContents(): void
    {
        require_once dirname(__DIR__, 2) . '/migrations/db/20261029111000_drop_unused_delivery_model_anr_column.php';
        $adapter = new SQLiteAdapter(['name' => ':memory:'], null, new NullOutput());
        $pdo = $adapter->getConnection();
        $pdo->exec('CREATE TABLE deliveries_models (
            id INTEGER PRIMARY KEY, anr_id INTEGER, category INTEGER, description1 TEXT, path1 TEXT
        )');
        $pdo->exec('CREATE INDEX deliveries_anr ON deliveries_models (anr_id)');
        $pdo->exec("INSERT INTO deliveries_models VALUES (1, 8, 2, 'Retained template', './template.docx')");
        $migration = new \DropUnusedDeliveryModelAnrColumn('testing', 20261029111000, null, new NullOutput());
        $migration->setAdapter($adapter);
        $migration->up();
        $migration->up();
        self::assertFalse($adapter->hasColumn('deliveries_models', 'anr_id'));
        self::assertSame([
            'id' => 1, 'category' => 2, 'description1' => 'Retained template', 'path1' => './template.docx',
        ], $pdo->query('SELECT * FROM deliveries_models')->fetch(\PDO::FETCH_ASSOC));
    }
}
