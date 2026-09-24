<?php
declare(strict_types=1);
namespace Tests\Unit\Taxonomy;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;use PHPUnit\Framework\TestCase;
final class SubjectTaxonomyServiceTest extends TestCase { public function testScoresEquivalentAccentedNamesAsExactMatch():void { self::assertSame(1.0,(new SubjectTaxonomyService())->similarity('Segurança','Seguranca')); } public function testScoresRelatedNamesBelowExactMatch():void { self::assertGreaterThan(0.5,(new SubjectTaxonomyService())->similarity('Redes de Computadores','Rede de Computador')); } }
