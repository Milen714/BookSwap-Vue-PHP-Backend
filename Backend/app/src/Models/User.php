<?php

namespace App\Models;

use DateTime;
use App\Models\UserRole;

class User{
    public ?int $id = null;
    public string $fname;
    public string $lname;
    public UserRole $role;
    public string $email;
    public string $password_hash;
    public ?string $address;
    public ?string $post_code;
    public ?string $country;
    public ?string $state;
    public ?string $resset_token;
    public ?DateTime $resset_token_expiry;
    public ?DateTime $joined_at;
    public ?bool $isActive;
    public ?bool $isVerified;
    public int $swapTokens;

    public function __construct(){}
    
    public static function fromArray(array $data): User {
        $user = new User();
        $user->id = $data['id'] ?? null;
        $user->fname = $data['fname'] ?? '';
        $user->lname = $data['lname'] ?? '';
        $user->role = UserRole::from($data['role'] ?? 'USER');
        $user->email = $data['email'] ?? '';

        if (!empty($data['password_hash'])) {
            $user->password_hash = $data['password_hash'];
        } else {
            $rawPassword = $data['password'] ?? '';
            $user->password_hash = $rawPassword !== '' ? password_hash($rawPassword, PASSWORD_BCRYPT) : '';
        }

        $user->address = $data['address'] ?? null;
        $user->post_code = $data['post_code'] ?? null;
        $user->country = $data['country'] ?? null;
        $user->resset_token = $data['resset_token'] ?? null;
        $user->resset_token_expiry = !empty($data['resset_token_expiry']) ? new DateTime($data['resset_token_expiry']) : null;
        $user->isActive = array_key_exists('isActive', $data) ? (bool)$data['isActive'] : true;
        $user->isVerified = array_key_exists('isVerified', $data) ? (bool)$data['isVerified'] : false;
        $user->swapTokens = (int)($data['swapTokens'] ?? $data['swap_tokens'] ?? 0);
        $user->state = $data['state'] ?? null;
        return $user;
    }
    public function fromPost(): User {
        $user = new User();
        $user->fname = $_POST['fname'] ?? '';
        $user->lname = $_POST['lname'] ?? '';
        $user->email = $_POST['email'] ?? '';
        $user->role = UserRole::USER;
        $user->password_hash = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $user->address = $_POST['address'] ?? null;
        $user->post_code = $_POST['post_code'] ?? null;
        $user->country = $_POST['country'] ?? null;
        $user->state = $_POST['state'] ?? null;
        $user->isActive = true;
        $user->isVerified = false;
        $user->swapTokens = 0;
        return $user;
    }
    public function fromJson(string $json): User {
        $data = json_decode($json, true);
        return self::fromArray($data);
    }
}