<?php
declare(strict_types=1);
namespace Tests\Unit\Study;
use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Service\CreateNotebookService;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use PHPUnit\Framework\TestCase;
final class CreateNotebookServiceTest extends TestCase {
 public function testFreezesTheRequestedQuestionSelection():void {$repo=new class implements NotebookRepositoryInterface { public ?Notebook $saved=null; public function save(Notebook $notebook):void{$this->saved=$notebook;} public function findByIdForUser(string $id,string $userId):?Notebook{return null;} };$response=(new CreateNotebookService($repo,new NotebookResponseMapper()))->create(new CreateNotebookRequestDto('user','Nome','STUDY',2,['q1','q2']));self::assertSame(['q1','q2'],$response->questionIds);self::assertSame(['q1','q2'],$repo->saved->selection->questionIds);}
}
