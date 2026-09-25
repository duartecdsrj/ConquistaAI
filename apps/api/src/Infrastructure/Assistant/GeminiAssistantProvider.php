<?php
declare(strict_types=1);
namespace App\Infrastructure\Assistant;
use App\Application\Assistant\Port\AssistantProviderInterface;
use App\Domain\Assistant\ValueObject\AssistantGeneratedAnswer;
final class GeminiAssistantProvider implements AssistantProviderInterface {
 public function __construct(private readonly string $apiKey,private readonly string $model) {}
 public function answer(string $question,array $evidence):AssistantGeneratedAnswer {
  if($this->apiKey==='') throw new \DomainException('GEMINI_API_KEY nao foi configurada.');
  $sources=implode("\n\n",array_map(static fn($e)=>"[Página {$e->pageNumber}]\n{$e->excerpt}",$evidence));
  $payload=['systemInstruction'=>['parts'=>[['text'=>'Você é um assistente de estudos. Responda em português do Brasil usando exclusivamente as fontes fornecidas. Se elas não sustentarem a resposta, diga isso claramente. Cite as páginas relevantes e não invente regras, datas ou links.']]],'contents'=>[['role'=>'user','parts'=>[['text'=>"Pergunta do estudante:\n{$question}\n\nFontes recuperadas do edital:\n{$sources}"]]]]];
  $url='https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($this->model).':generateContent?key=' . rawurlencode($this->apiKey);
  $context=stream_context_create(['http'=>['method'=>'POST','timeout'=>60,'ignore_errors'=>true,'header'=>"Content-Type: application/json\r\n",'content'=>json_encode($payload,JSON_THROW_ON_ERROR)]]);
  $body=false;for($attempt=0;$attempt<3&&$body===false;$attempt++){if($attempt>0)usleep($attempt*1000000);$body=@file_get_contents($url,false,$context);}
  if($body===false) throw new \DomainException('Nao foi possivel consultar o Gemini.');
  try{$response=json_decode($body,true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new \DomainException('O Gemini retornou uma resposta invalida.');}
  $parts=$response['candidates'][0]['content']['parts']??[];
  $text=is_array($parts)?trim(implode('',array_map(static fn($part)=>is_array($part)&&is_string($part['text']??null)?$part['text']:'',$parts))):'';
  if($text===''){ $message=$response['error']['message']??null; throw new \DomainException(is_string($message)?'Gemini: '.$message:'O Gemini nao retornou texto.'); }
  return new AssistantGeneratedAnswer($text,'gemini',$this->model,$evidence);
 }
}
