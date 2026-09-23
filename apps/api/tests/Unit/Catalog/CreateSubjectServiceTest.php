<?php
declare(strict_types=1);
namespace Tests\Unit\Catalog;
use App\Application\Catalog\DTO\Request\CreateSubjectRequestDto;
use App\Application\Catalog\Mapper\SubjectResponseMapper;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Application\Catalog\Service\CreateSubjectService;
use App\Domain\Catalog\Entity\Subject;
use App\Domain\Catalog\Entity\Syllabus;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;
use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;
use PHPUnit\Framework\TestCase;
final class CreateSubjectServiceTest extends TestCase {
 public function testRejectsUnknownSyllabus():void{
  $subjects=new class implements SubjectRepositoryInterface{public function save(Subject $s):void{}public function existsForSyllabus(string $id,string $syllabusId):bool{return false;}public function listForSyllabus(string $id):array{return[];}};
  $syllabi=new class implements SyllabusRepositoryInterface{public function save(Syllabus $s):void{}public function existsById(string $id):bool{return false;}public function existsForPosition(string $id,string $p):bool{return false;}public function listForPosition(string $p):array{return[];}};
  $tx=new class implements TransactionManagerInterface{public function transactional(callable $callback):mixed{return $callback();}};
  $this->expectException(\DomainException::class);
  (new CreateSubjectService($subjects,$syllabi,new SubjectResponseMapper(),$tx))->create(new CreateSubjectRequestDto('missing',null,'Redes',0));
 }
 public function testRejectsParentFromAnotherSyllabus():void{
  $subjects=new class implements SubjectRepositoryInterface{public function save(Subject $s):void{}public function existsForSyllabus(string $id,string $syllabusId):bool{return false;}public function listForSyllabus(string $id):array{return[];}};
  $syllabi=new class implements SyllabusRepositoryInterface{public function save(Syllabus $s):void{}public function existsById(string $id):bool{return true;}public function existsForPosition(string $id,string $p):bool{return false;}public function listForPosition(string $p):array{return[];}};
  $tx=new class implements TransactionManagerInterface{public function transactional(callable $callback):mixed{return $callback();}};
  $this->expectException(\DomainException::class);
  (new CreateSubjectService($subjects,$syllabi,new SubjectResponseMapper(),$tx))->create(new CreateSubjectRequestDto('s1','foreign','Redes',0));
 }
}
