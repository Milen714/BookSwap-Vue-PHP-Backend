<?php

namespace App\Models\DTOs;
use App\Models\User;

class UserDTO
{
    public int $id;
    public string $fname;
    public string $lname;
    public string $email;
    public int $swapTokens;

    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->fname = $user->fname;
        $this->lname = $user->lname;
        $this->email = $user->email;
        $this->swapTokens = $user->swapTokens;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'swapTokens' => $this->swapTokens
        ];
    }
}