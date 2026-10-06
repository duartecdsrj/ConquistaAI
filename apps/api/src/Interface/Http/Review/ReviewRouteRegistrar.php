<?php
declare(strict_types=1);

namespace App\Interface\Http\Review;

use App\Application\Identity\Service\AuthService;
use App\Application\Review\Mapper\FlashcardReviewResponseMapper;
use App\Application\Review\Mapper\NotebookAnalysisResponseMapper;
use App\Application\Review\Mapper\ReviewResponseMapper;
use App\Application\Review\Service\BuildReviewSessionService;
use App\Application\Review\Service\GetMasteryMapService;
use App\Application\Review\Service\GetNotebookAnalysisService;
use App\Application\Review\Service\NavigateReviewSessionService;
use App\Application\Review\Service\RateFlashcardService;
use App\Application\Review\Service\CobitGameService;
use App\Domain\Review\Enum\ReviewSessionKind;
use App\Domain\Review\Service\InitialReviewPriorityCalculator;
use App\Domain\Review\Service\InitialSpacedRepetitionStrategy;
use App\Domain\Review\ValueObject\ReviewAlgorithmConfiguration;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineFlashcardRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineFlashcardReviewRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineNotebookAnalysisActionRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineNotebookAnalysisExecutionRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineReviewSessionRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineUserConceptMasteryRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineUserFlashcardProgressRepository;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Review\Controller\ReviewRatingController;
use App\Interface\Http\Review\Controller\ReviewReadController;
use App\Interface\Http\Review\Controller\ReviewSessionController;
use App\Interface\Http\Review\Controller\CobitGameController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class ReviewRouteRegistrar
{
    public function __construct(private ApiResponseFactory $responses, private AuthService $auth) {}

    public function register(App $app): void
    {
        $em = DoctrineEntityManagerFactory::create();
        $cards = new DoctrineFlashcardRepository($em);
        $progress = new DoctrineUserFlashcardProgressRepository($em);
        $mastery = new DoctrineUserConceptMasteryRepository($em);
        $sessions = new DoctrineReviewSessionRepository($em);
        $identity = new IdentityRequestFactory();
        $requests = new ReviewRequestFactory();
        $analysisExecutions = new DoctrineNotebookAnalysisExecutionRepository($em);
        $analysisActions = new DoctrineNotebookAnalysisActionRepository($em);
        $read = new ReviewReadController($this->auth, new GetMasteryMapService($mastery, new DoctrineTaxonomySubjectRepository($em)), new GetNotebookAnalysisService($analysisExecutions, $analysisActions, new NotebookAnalysisResponseMapper()), $this->responses);
        $reviews = new DoctrineFlashcardReviewRepository($em);
        $transactions = new DoctrineTransactionManager($em);
        $builder = new BuildReviewSessionService($sessions, $progress, $mastery, $cards, $reviews, new InitialReviewPriorityCalculator(), $transactions);
        $session = new ReviewSessionController($this->auth, $builder, new NavigateReviewSessionService($builder, $transactions), $progress, new ReviewResponseMapper(), $reviews, $this->responses);
        $rating = new ReviewRatingController($this->auth, new RateFlashcardService($sessions, $progress, $reviews, new InitialSpacedRepetitionStrategy(new ReviewAlgorithmConfiguration()), $transactions), $builder, $sessions, $progress, new FlashcardReviewResponseMapper(), new ReviewResponseMapper(), $this->responses);
        $cobit = new CobitGameController($this->auth, new CobitGameService(), $this->responses);

        $app->get('/v1/review/mastery-map', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $read->mastery($r, $p, $identity->accessToken($r), max(1, (int) ($r->getQueryParams()['page'] ?? 1)), min(100, max(1, (int) ($r->getQueryParams()['per_page'] ?? 25)))));
        $app->get('/v1/review/games/cobit-4-1', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $cobit->exercise($r, $p, $identity->accessToken($r)));
        $app->post('/v1/review/games/cobit-4-1/answers', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $cobit->evaluate($r, $p, $identity->accessToken($r), $requests->cobitAnswers($r)));
        $app->get('/v1/review/sessions/daily', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $session->get($r, $p, $identity->accessToken($r), ReviewSessionKind::DAILY, min(100, max(1, (int) ($r->getQueryParams()['limit'] ?? 20)))));
        $app->get('/v1/review/sessions/quick', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $session->get($r, $p, $identity->accessToken($r), ReviewSessionKind::QUICK, min(100, max(1, (int) ($r->getQueryParams()['limit'] ?? 10)))));
        $app->get('/v1/review/sessions/advance', static fn (ServerRequestInterface $r, ResponseInterface $p): ResponseInterface => $session->get($r, $p, $identity->accessToken($r), ReviewSessionKind::ADVANCE, min(100, max(1, (int) ($r->getQueryParams()['limit'] ?? 10)))));
        $app->post('/v1/review/sessions/{sessionId}/navigation', function (ServerRequestInterface $r, ResponseInterface $p, array $a) use ($session, $identity, $requests): ResponseInterface { return $session->navigate($r,$p,$identity->accessToken($r),(string)($a['sessionId']??''),$requests->navigation($r)); });
        $app->post('/v1/review/sessions/{sessionId}/cards/{cardId}/reviews', function (ServerRequestInterface $r, ResponseInterface $p, array $a) use ($rating, $identity, $requests): ResponseInterface {
            try {
                return $rating->rate($r, $p, $identity->accessToken($r), $requests->rate($r, (string) ($a['sessionId'] ?? ''), (string) ($a['cardId'] ?? '')));
            } catch (\InvalidArgumentException) {
                return $this->responses->problem($p, 'VALIDATION_FAILED', 'Classificação inválida.', 422, (string) $r->getAttribute('request_id'));
            }
        });
        $app->get('/v1/study/notebooks/{id}/analysis', static fn (ServerRequestInterface $r, ResponseInterface $p, array $a): ResponseInterface => $read->analysis($r, $p, $identity->accessToken($r), (string) ($a['id'] ?? '')));
    }
}
