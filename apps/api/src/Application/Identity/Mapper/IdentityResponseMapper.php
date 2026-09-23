<?php
declare(strict_types=1);
namespace App\Application\Identity\Mapper;
use App\Application\Identity\DTO\Response\AuthenticationResponseDto;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Domain\Identity\Entity\User;
final class IdentityResponseMapper {
 public function authentication(string $accessToken,string $refreshToken,User $user): AuthenticationResponseDto { return new AuthenticationResponseDto($accessToken,$refreshToken,$this->currentUser($user)); }
 public function currentUser(User $user): CurrentUserResponseDto { return new CurrentUserResponseDto($user->id,$user->email,$user->name,$user->roles); }
}
