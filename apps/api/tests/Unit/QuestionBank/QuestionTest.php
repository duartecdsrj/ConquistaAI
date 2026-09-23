<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Domain\QuestionBank\Entity\Question;
use App\Domain\QuestionBank\Entity\QuestionOption;
use PHPUnit\Framework\TestCase;
final class QuestionTest extends TestCase {
 public function testPublishesOnlyWithAValidAnswerKey():void {$question=new Question('q','Texto','DRAFT',[new QuestionOption('a','A','A',1),new QuestionOption('b','B','B',2)],'a');$question->publish();self::assertTrue($question->isPublished());}
}
