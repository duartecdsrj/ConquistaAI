<?php
declare(strict_types=1);

namespace Tests\Unit\Study;

use App\Interface\Http\Study\StudyRequestFactory;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

final class StudyRequestFactoryTest extends TestCase
{
    public function testAcceptsAnEmptyFiltersObject(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/v1/notebooks');
        $request->getBody()->write('{"name":"teste","mode":"STUDY","quantity":3,"filters":{}}');

        $input = (new StudyRequestFactory())->createNotebook($request);

        self::assertSame('teste', $input->name);
        self::assertSame('STUDY', $input->mode);
        self::assertSame(3, $input->quantity);
        self::assertSame([], $input->filters);
    }
}
