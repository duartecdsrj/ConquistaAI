<?php
declare(strict_types=1);
namespace App\Domain\Review\ValueObject;
use App\Domain\Review\Enum\FlashcardType;
final readonly class FlashcardFingerprint {
 public const VERSION=1;
 public function __construct(public int $version,public string $value){if($version<1||!preg_match('/^[a-f0-9]{64}$/',$value))throw new \InvalidArgumentException('Fingerprint de flashcard inválido.');}
 public static function fromContent(string $conceptId,FlashcardType $type,string $front,string $back):self { $normalize=static function(string $value):string{$value=mb_strtolower(trim($value),'UTF-8');return preg_replace('/\s+/u',' ',$value)??$value;};return new self(self::VERSION,hash('sha256',implode("\n",[$conceptId,$type->value,$normalize($front),$normalize($back)]))); }
}
