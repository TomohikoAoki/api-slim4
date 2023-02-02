<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserRepository;
use App\Models\User;

class ExposedUserRepository implements UserRepository
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function findUserOfUid(string $uId)
    {
        $user = $this->user->where('uid', $uId)->firstOrFail();

        return $user;
    }
}