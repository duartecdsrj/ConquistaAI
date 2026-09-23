<?php
declare(strict_types=1);
namespace App\Domain\Study\Entity;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Enum\NotebookStatus;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
final class Notebook {
 public function __construct(public readonly string $id,public readonly string $userId,public readonly string $name,public readonly NotebookMode $mode,public readonly FrozenQuestionSelection $selection,public readonly \DateTimeImmutable $createdAt,public NotebookStatus $status=NotebookStatus::DRAFT,public ?\DateTimeImmutable $startedAt=null,public ?\DateTimeImmutable $finishedAt=null,public ?int $durationSeconds=null) {}
 public static function create(string $userId,string $name,NotebookMode $mode,FrozenQuestionSelection $selection,\DateTimeImmutable $now):self { if(trim($name)==='')throw new \DomainException('Notebook name is required.');return new self(self::uuid(),$userId,trim($name),$mode,$selection,$now); }
 public function start(\DateTimeImmutable $now):void { if($this->status===NotebookStatus::FINISHED)throw new \DomainException('Caderno finalizado nao pode ser iniciado novamente.');if($this->status===NotebookStatus::IN_PROGRESS)return;$this->durationSeconds ??= 0;$this->startedAt=$now;$this->status=NotebookStatus::IN_PROGRESS; }
 public function pause(\DateTimeImmutable $now):void { if($this->status!==NotebookStatus::IN_PROGRESS||$this->startedAt===null)throw new \DomainException('Caderno nao esta em execucao.');$this->durationSeconds=$this->elapsedSecondsAt($now);$this->startedAt=null;$this->status=NotebookStatus::PAUSED; }
 public function finish(\DateTimeImmutable $now):void { if($this->status===NotebookStatus::FINISHED)throw new \DomainException('Caderno ja foi finalizado.');if($this->status===NotebookStatus::DRAFT)$this->start($now);$this->durationSeconds=$this->elapsedSecondsAt($now);$this->startedAt=null;$this->finishedAt=$now;$this->status=NotebookStatus::FINISHED; }
 public function elapsedSecondsAt(\DateTimeImmutable $now):int { $elapsed=$this->durationSeconds ?? 0;if($this->status===NotebookStatus::IN_PROGRESS&&$this->startedAt!==null)$elapsed+=max(0,$now->getTimestamp()-$this->startedAt->getTimestamp());return $elapsed; }
 private static function uuid():string{$b=random_bytes(16);$b[6]=chr((ord($b[6])&15)|64);$b[8]=chr((ord($b[8])&63)|128);return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($b),4));}
}
