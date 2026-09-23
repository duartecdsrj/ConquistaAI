<?php
declare(strict_types=1);
namespace App\Domain\Identity\ValueObject;
final readonly class RefreshToken { private function __construct(private string $plainText) {} public static function fromPlainText(string $plainText):self { if ($plainText === '') { throw new \InvalidArgumentException('A refresh token is required.'); } return new self($plainText); } public function hash():string{return hash('sha256',$this->plainText);} }
