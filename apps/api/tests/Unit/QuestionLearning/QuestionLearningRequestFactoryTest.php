<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionLearning;

use App\Interface\Http\QuestionLearning\QuestionLearningRequestFactory;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

final class QuestionLearningRequestFactoryTest extends TestCase
{
    public function testBuildsInteractionFromJsonWithoutParsedBodyMiddleware(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/v1/questions/question-1/learning-interactions')
            ->withBody((new StreamFactory())->createStream('{"favorite":true,"review_later":false,"not_mastered":true}'));
        self::assertNull($request->getParsedBody());
        $dto = (new QuestionLearningRequestFactory())->interaction('question-1', $request);
        self::assertTrue($dto->favorite ?? false);
        self::assertFalse($dto->reviewLater ?? true);
        self::assertTrue($dto->notMastered ?? false);
    }

    public function testBuildsNoteFromJsonWithoutParsedBodyMiddleware(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/')->withBody((new StreamFactory())->createStream('{"content":"minha nota"}'));
        $dto = (new QuestionLearningRequestFactory())->note('question-1', $request);
        self::assertSame('minha nota', $dto->content);
    }
}
