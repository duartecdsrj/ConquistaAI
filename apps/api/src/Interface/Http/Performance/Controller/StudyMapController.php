<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Performance\DTO\Request\GetStudyMapRequestDto;
use App\Application\Performance\DTO\Request\SaveStudyMapScheduleRequestDto;
use App\Application\Performance\Mapper\StudyMapScheduleResponseMapper;
use App\Application\Performance\Service\GetStudyMapService;
use App\Application\Performance\Service\SaveStudyMapScheduleService;
use App\Infrastructure\Http\ApiResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StudyMapController
{
    public function __construct(private AuthService $auth, private GetStudyMapService $map, private SaveStudyMapScheduleService $schedule, private StudyMapScheduleResponseMapper $mapper, private ApiResponseFactory $responses) {}

    public function get(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $examId, ?\DateTimeImmutable $from, ?\DateTimeImmutable $to): ResponseInterface
    {
        try {
            $user = $this->auth->currentUser($access);
            return $this->responses->success($response, $this->map->execute(new GetStudyMapRequestDto($user->id, $examId, $from, $to)), (string) $request->getAttribute('request_id'));
        } catch (\InvalidArgumentException) {
            return $this->responses->problem($response, 'VALIDATION_FAILED', 'Parâmetros do mapa de estudo são inválidos.', 422, (string) $request->getAttribute('request_id'));
        }
    }

    public function save(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, SaveStudyMapScheduleRequestDto $input): ResponseInterface
    {
        try {
            $user = $this->auth->currentUser($access);
            $saved = $this->schedule->execute(new SaveStudyMapScheduleRequestDto($user->id, $input->examId, $input->subjectId, $input->startDate, $input->endDate, $input->status, $input->predecessorSubjectIds, $input->estimatedMinutes), new \DateTimeImmutable('now'));
            return $this->responses->success($response, $this->mapper->map($saved), (string) $request->getAttribute('request_id'));
        } catch (\InvalidArgumentException) {
            return $this->responses->problem($response, 'VALIDATION_FAILED', 'Cronograma de estudo inválido.', 422, (string) $request->getAttribute('request_id'));
        }
    }
}
