<?php
declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $username;
    
    /**
     * @var string
     */
    private $password;

   
    /**
     * @param int|null  $id
     * @param string    $username
     * @param string    $password         */
    public function __construct(?int $id, string $username, string $password)
    {
        $this->id = $id;
        $this->username = strtolower($username);
        $this->password = ucfirst($password);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            ];
    }
}
