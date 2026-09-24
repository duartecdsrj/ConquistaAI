<?php
declare(strict_types=1);
namespace App\Application\Performance\Service;
use App\Application\Performance\DTO\Response\StudyPlanResponseDto;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
final class GetStudyPlanService {
 public function __construct(private readonly PerformanceStatisticsRepositoryInterface $statistics) {}
 public function getForUser(string $userId): StudyPlanResponseDto {
  $groups=[];foreach($this->statistics->completedPlanAnswersForUser($userId) as $answer){$groups[$answer->subjectId]??=['total'=>0,'correct'=>0,'days'=>[]];$groups[$answer->subjectId]['total']++;$groups[$answer->subjectId]['correct']+=$answer->isCorrect?1:0;$groups[$answer->subjectId]['days'][$answer->completedAt->format('Y-m-d')]=true;}
  $priorities=[];foreach($groups as $subjectId=>$values){$total=$values['total'];$days=count($values['days']);$percentage=round(($values['correct']/$total)*100,2);$sufficient=$total>=10&&$days>=3;$priorities[]=['subjectId'=>$subjectId,'total'=>$total,'correct'=>$values['correct'],'percentage'=>$percentage,'distinctDays'=>$days,'sufficientData'=>$sufficient,'reason'=>$sufficient?sprintf('%d%% de acerto em %d respostas distribuídas por %d dias.',$percentage,$total,$days):sprintf('%d respostas em %d dia(s): ainda há dados insuficientes para classificar desempenho.',$total,$days),'action'=>$sufficient?'RESPONDER_CONJUNTO_FILTRADO':'PRATICAR_AMOSTRA'];}
  usort($priorities,static fn(array $a,array $b):int=>[$a['sufficientData']?0:1,$a['percentage'],$a['subjectId']]<=>[$b['sufficientData']?0:1,$b['percentage'],$b['subjectId']]);return new StudyPlanResponseDto(array_slice($priorities,0,5));
 }
}
