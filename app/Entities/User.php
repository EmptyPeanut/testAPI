<?php
namespace App\Entities;

use JsonSerializable;

class User implements JsonSerializable{
    private int|null    $id;
    private string      $username;
    private string      $firstName;
    private string      $lastName;
    private int|null    $age;
    private string      $password;

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->name;
        }
    }
    public function getId(): int{
        return $this->id;
    }
    public function getUsername(): string{
        return $this->username;
    }
    public function getFirstName(): string{
        return $this->firstName;
    }
    public function getLastName(): string{
        return $this->lastName;
    }
    public function getAge(): int|null{
        return $this->age;
    }
    public function getPassword(): string{
        return $this->password;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }
    public function setUsername(string $username): void {
        $this->username = $username;
    }
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }
    public function setAge(int|null $age): void {
        $this->age = $age;
    }
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    // Peut-être voué à changer, je n'aime pas le fait de choisir ici ce que je renvoie
    //ça fonctionne nonobstant
    public function jsonSerialize(): mixed {
        return [
            'id'        => $this->getId(),
            'username'  => $this->getUsername(),
            'firstName' => $this->getFirstName(),
            'lastName'  => $this->getLastName(),
            'age'       => $this->getAge()
        ];
    }
}