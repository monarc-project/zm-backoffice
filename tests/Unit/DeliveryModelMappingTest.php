<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Monarc\Core\Entity\DeliveriesModels;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class DeliveryModelMappingTest extends TestCase
{
    public function testBackOfficeTemplatesPersistAndReloadWithoutAnAnalysisColumn(): void
    {
        $manager = $this->manager();
        try {
            $metadata = $manager->getClassMetadata(DeliveriesModels::class);
            self::assertFalse($metadata->hasAssociation('anr'));
            self::assertSame([], $metadata->table['indexes'] ?? []);
            (new SchemaTool($manager))->createSchema([$metadata]);
            $template = new DeliveriesModels();
            $template->set('category', 1)->set('editable', true)
                ->setDescriptions(['description1' => 'Existing template']);
            $manager->persist($template);
            $manager->flush();
            $id = $template->get('id');
            $manager->clear();
            self::assertSame('Existing template', $manager->find(DeliveriesModels::class, $id)->getDescription(1));
            self::assertArrayNotHasKey('anr_id', $manager->getConnection()->createSchemaManager()
                ->listTableColumns('deliveries_models'));
        } finally {
            $manager->close();
        }
    }

    public function testTheSharedCoreEntityDeclaresItsOwnTemplateFields(): void
    {
        $manager = $this->manager();
        try {
            $class = new \ReflectionClass(DeliveriesModels::class);
            self::assertFalse($class->hasProperty('anr'));
            self::assertSame(DeliveriesModels::class, $class->getProperty('category')->getDeclaringClass()->getName());
            self::assertFalse($manager->getClassMetadata(DeliveriesModels::class)->isMappedSuperclass);
        } finally {
            $manager->close();
        }
    }

    private function manager(): EntityManager
    {
        $config = new Configuration();
        $config->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader()));
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('BackOfficeDeliveryModelProxies');

        return EntityManager::create(['driver' => 'pdo_sqlite', 'memory' => true], $config);
    }
}
