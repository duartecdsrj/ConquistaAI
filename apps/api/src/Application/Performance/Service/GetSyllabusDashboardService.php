<?php
declare(strict_types=1);
namespace App\Application\Performance\Service;
use App\Application\Performance\DTO\Request\GetSyllabusDashboardRequestDto;
use App\Application\Performance\DTO\Response\SyllabusDashboardResponseDto;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
final class GetSyllabusDashboardService {
 public function __construct(private readonly PerformanceStatisticsRepositoryInterface $statistics) {}
 public function getForUser(string $userId, GetSyllabusDashboardRequestDto $request): SyllabusDashboardResponseDto {
  $syllabi=$this->statistics->syllabiWithCompletedAnswersForUser($userId);$selectedId=$request->syllabusId??$syllabi[0]->id??null;$options=array_map(static fn($item):array=>['id'=>$item->id,'name'=>$item->name,'positionName'=>$item->positionName,'examName'=>$item->examName],$syllabi);
  if($selectedId===null||!in_array($selectedId,array_column($options,'id'),true))return new SyllabusDashboardResponseDto($options,null,0,0,0,0.0,0.0,false,0,[]);
  $answers=$this->statistics->completedAnswersForUserAndSyllabus($userId,$selectedId);$nodes=$this->statistics->taxonomyHierarchyForSyllabus($selectedId);$byId=[];foreach($nodes as $node)$byId[$node->id]=$node;$metrics=[];
  foreach($answers as $answer)foreach(array_unique($answer->taxonomySubjectIds)as $subjectId){$visited=[];while(isset($byId[$subjectId])&&!isset($visited[$subjectId])){$visited[$subjectId]=true;$metrics[$subjectId]??=['total'=>0,'correct'=>0,'days'=>[]];$metrics[$subjectId]['total']++;$metrics[$subjectId]['correct']+=$answer->isCorrect?1:0;$metrics[$subjectId]['days'][$answer->completedAt->format('Y-m-d')]=true;$subjectId=$byId[$subjectId]->parentId;}}
  $subjects=[];foreach($metrics as $id=>$metric){$node=$byId[$id];$total=$metric['total'];$days=count($metric['days']);$subjects[]=['id'=>$id,'parentId'=>$node->parentId,'name'=>$node->name,'total'=>$total,'correct'=>$metric['correct'],'incorrect'=>$total-$metric['correct'],'percentage'=>round(($metric['correct']/$total)*100,2),'distinctDays'=>$days,'sufficientData'=>$total>=10&&$days>=3];}usort($subjects,static fn(array $a,array $b):int=>[$a['name'],$a['id']]<=>[$b['name'],$b['id']]);
  $total=count($answers);$correct=count(array_filter($answers,static fn($answer):bool=>$answer->isCorrect));$elapsed=array_sum(array_map(static fn($answer):int=>$answer->elapsedSeconds,$answers));$days=count(array_unique(array_map(static fn($answer):string=>$answer->completedAt->format('Y-m-d'),$answers)));
  return new SyllabusDashboardResponseDto($options,$selectedId,$total,$correct,$total-$correct,$total?round(($correct/$total)*100,2):0.0,$total?round($elapsed/$total,2):0.0,$total>=10&&$days>=3,$days,$subjects);
 }
}
