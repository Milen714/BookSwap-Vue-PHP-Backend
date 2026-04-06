<?php

namespace App\Models\DTOs;

use App\Models\Enums\UserRole;
use App\Models\User;

class UserDTO
{
    public int $id;
    public string $fname;
    public string $lname;
    public string $email;
    public ?string $phone_number;
    public ?string $bio;
    public int $swapTokens;
    public ?string $address;
    public ?string $state;
    public ?string $country;
    public ?string $post_code;
    public ?UserRole $role;


    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->fname = $user->fname;
        $this->lname = $user->lname;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->bio = $user->bio;
        $this->swapTokens = $user->swapTokens;
        $this->address = $user->address;
        $this->state = $user->state;
        $this->country = $user->country;
        $this->post_code = $user->post_code;
        $this->role = $user->role;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'bio' => $this->bio,
            'swapTokens' => $this->swapTokens,
            'address' => $this->address,
            'state' => $this->state,
            'country' => $this->country,
            'post_code' => $this->post_code,
            'role' => $this->role?->value

        ];
    }
}