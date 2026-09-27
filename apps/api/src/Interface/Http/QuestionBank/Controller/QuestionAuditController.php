<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank\Controller;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Service\GetLatestQuestionAuditReportService;
use App\Application\QuestionBank\Service\ListLatestQuestionAuditFindingsService;
use App\Application\QuestionBank\DTO\Request\ListQuestionAuditFindingsRequestDto;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
final class QuestionAuditController {
    public function __construct(private readonly AuthService $auth, private readonly GetLatestQuestionAuditReportService $reports, private readonly ListLatestQuestionAuditFindingsService $findings, private readonly ApiResponseFactory $responses) {}
    public function latest(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface {
        try { $user = $this->auth->currentUser($access); } catch (DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        if (!in_array('ADMIN', $user->roles, true)) return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, (string) $request->getAttribute('request_id'));
        $report = $this->reports->get();
        if ($report === null) return $this->responses->problem($response, 'RESOURCE_NOT_FOUND', 'Nenhuma auditoria foi executada.', 404, (string) $request->getAttribute('request_id'));
        return $this->responses->success($response, $report, (string) $request->getAttribute('request_id'));
    }
    public function findings(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, ListQuestionAuditFindingsRequestDto $input): ResponseInterface {
        try { $user = $this->auth->currentUser($access); } catch (DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        if (!in_array('ADMIN', $user->roles, true)) return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, (string) $request->getAttribute('request_id'));
        $page = $this->findings->list($input);
        if ($page === null) return $this->responses->problem($response, 'RESOURCE_NOT_FOUND', 'Nenhuma auditoria foi executada.', 404, (string) $request->getAttribute('request_id'));
        return $this->responses->paginated($response, $page->items, (string) $request->getAttribute('request_id'), $page->page, $page->perPage, $page->total);
    }
}
