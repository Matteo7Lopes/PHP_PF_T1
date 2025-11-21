<?php
namespace App\Helpers;
class Clean{

    public function lastname(string $lastname): string{
        return strtoupper(trim($lastname));
    }

}