<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\DTO\Request\ListPublishedQuestionsRequestDto;
use App\Application\QuestionBank\Service\ListPublishedQuestionsService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PublishedQuestionController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly ListPublishedQuestionsService $questions,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function list(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        ListPublishedQuestionsRequestDto $input,
    ): ResponseInterface {
        try {
            $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem(
                $response,
                'UNAUTHENTICATED',
                'Credenciais invalidas ou expiradas.',
                401,
                (string) $request->getAttribute('request_id'),
            );
        }

        $page = $this->questions->list($input);

        return $this->responses->paginated(
            $response,
            $page['items'],
            (string) $request->getAttribute('request_id'),
            $page['page'],
            $page['perPage'],
            $page['total'],
        );
    }
}
