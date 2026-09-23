<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Response\ImportRowValidationResponseDto;
use App\Application\QuestionBank\DTO\Response\ImportValidationReportResponseDto;
final class QuestionImportValidationService {
    /** @param list<array<string,mixed>> $rows */
    public function validate(array $rows): ImportValidationReportResponseDto {
        $result=[];$valid=0;$invalid=0;
        foreach($rows as $index=>$row){$errors=[];$statement=$row['statement']??null;$options=$row['options']??null;$correct=$row['correct_option']??null;
            if(!is_string($statement)||trim($statement)===''){$errors[]=['field'=>'statement','code'=>'REQUIRED','message'=>'Enunciado e obrigatorio.'];}
            if(!is_array($options)||count($options)<2){$errors[]=['field'=>'options','code'=>'MIN_ITEMS','message'=>'Informe ao menos duas alternativas.'];}
            if(!is_string($correct)||$correct===''){$errors[]=['field'=>'correct_option','code'=>'REQUIRED','message'=>'Informe o gabarito.'];}
            elseif(is_array($options)&&!in_array($correct,array_column($options,'id'),true)){$errors[]=['field'=>'correct_option','code'=>'INVALID_REFERENCE','message'=>'O gabarito deve referenciar uma alternativa.'];}
            $isValid=$errors===[];$isValid?$valid++:$invalid++;$result[]=new ImportRowValidationResponseDto($index+1,$isValid,$errors);
        } return new ImportValidationReportResponseDto($valid,$invalid,$result);
    }
}
