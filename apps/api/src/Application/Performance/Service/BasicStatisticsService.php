<?php
declare(strict_types=1);
namespace App\Application\Performance\Service;
use App\Application\Performance\DTO\Response\BasicStatisticsResponseDto;
use App\Domain\Performance\ValueObject\CompletedAnswer;
final class BasicStatisticsService {
    /** @param list<CompletedAnswer> $answers */
    public function calculate(array $answers): BasicStatisticsResponseDto {
        $total=count($answers);$correct=0;$elapsed=0;$subjects=[];
        foreach($answers as $answer){$correct += $answer->isCorrect ? 1 : 0;$elapsed += $answer->elapsedSeconds;$key=$answer->subjectId;$subjects[$key] ??=['total'=>0,'correct'=>0];$subjects[$key]['total']++;$subjects[$key]['correct'] += $answer->isCorrect ? 1 : 0;}
        $subjectMetrics=[];
        foreach($subjects as $id=>$values){$subjectMetrics[$id]=['total'=>$values['total'],'correct'=>$values['correct'],'incorrect'=>$values['total']-$values['correct'],'percentage'=>round(($values['correct']/$values['total'])*100,2)];}
        return new BasicStatisticsResponseDto($total,$correct,$total-$correct,$total===0?0.0:round(($correct/$total)*100,2),$total===0?0.0:round($elapsed/$total,2),$subjectMetrics);
    }
}
