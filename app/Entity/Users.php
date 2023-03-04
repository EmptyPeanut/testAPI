<?
namespace App\Entities;

class Users{
    private int     $id;
    private string  $name;
    private string  $lastName;
    private int     $age;
    private string  $password;

    public function getId(): int{
        return $this->id;
    }
    public function getName(): string{
        return $this->name;
    }
    public function getLastName(): string{
        return $this->lastName;
    }
    public function getAge(): int{
        return $this->age;
    }
    public function getPassword(): string{
        return $this->password;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }
    public function setAge(int $age): void {
        $this->age = $age;
    }
    public function setPassword(string $password): void {
        $this->password = $password;
    }
}