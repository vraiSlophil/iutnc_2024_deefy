<?php

namespace iutnc\deefy\models;

class Permission
{
    private int $permission_id;
    private string $role_name;
    private int $role_level;

    public function __construct(int $permission_id, string $role_name, int $role_level)
    {
        $this->permission_id = $permission_id;
        $this->role_name = $role_name;
        $this->role_level = $role_level;
    }

    public function getPermissionId(): int
    {
        return $this->permission_id;
    }

    public function getRoleName(): string
    {
        return $this->role_name;
    }

    public function getRoleLevel(): int
    {
        return $this->role_level;
    }
}
