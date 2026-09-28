<?php
declare(strict_types=1);
namespace Tests\Unit\Arena;
use App\Domain\Arena\Entity\Duel; use App\Domain\Arena\Repository\DuelQuestionSelectorInterface; use App\Domain\Arena\Repository\DuelRepositoryInterface; use App\Application\Arena\Service\DuelLifecycleService; use PHPUnit\Framework\TestCase;
final class DuelLifecycleServiceTest extends TestCase { public function testCreatesPrivateDuel():void{$repo=$this->createMock(DuelRepositoryInterface::class);$repo->expects(self::once())->method('save');$repo->expects(self::once())->method('addParticipant');$tx=new class implements \App\Application\Performance\Port\TransactionManagerInterface{public function transactional(callable $operation):mixed{return $operation();}};$d=(new DuelLifecycleService($repo,$this->createMock(DuelQuestionSelectorInterface::class),$tx))->create('u',2,1,5,30);self::assertSame('WAITING',$d->status->value);}}
