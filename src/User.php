<?php

namespace CT275\Project;

use PDO;

class User
{
    private ?PDO $db;

    private int $id = -1;
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    public $password;
    public $confirmPassword;
    public $role;
    public $created_at;
    public $updated_at;
    public $deleted;
    private array $errors = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }
    public function fill(array $data): User
    {
        $this->fullname = $data['fullname'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_number = $data['phone_number'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->confirmpassword = $data['confirm_password'] ?? '';
        $this->role = $data['role'] ?? '';
        return $this;
    }

    public function all(): array
        {
            $users = [];
            $statement = $this->db->prepare('select * from user');
            $statement->execute();
            while ($row = $statement->fetch()) {
            $user = new User($this->db);
            $user->fillFromDB($row);
            $users[] = $user;
            }
            return $users;
        }
    protected function fillFromDB(array $row): User
        {
            [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'password' => $this->password,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted
            ] = $row;
            return $this;
        }

    public function getValidationErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        $fullname = trim($this->fullname);
        if (!$fullname) {
            $this->errors['fullname'] = 'Họ tên không hợp lệ';
        }

        $validPhoneNumber = preg_match('/^[0-9]{10}$/', $this->phone_number);
        if (!$validPhoneNumber) {
            $this->errors['phone_number'] = 'Số điện thoại không hợp lệ';
        }

        $validEmail = preg_match('/^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-zA-Z]{2,5})$/',$this->email);
        if (!$validEmail) {
            $this->errors['email'] = 'Email không hợp lệ';
        }
        
        $minLength = 8;
        $hasUpper = preg_match('/[A-Z]/', $this->password);
        $hasLower = preg_match('/[a-z]/', $this->password);
        $hasNumber = preg_match('/[0-9]/', $this->password);
        $hasSpecial = preg_match('/[^\w\s]/', $this->password);

        $validpassword = $hasUpper && $hasLower && $hasNumber && $hasSpecial && strlen($this->password) >= $minLength;
        if (!$validpassword) {
            $this->errors['password'] = 'Mật khẩu không hợp lệ';
        }


        $validconfirmpassword = ($this->password === $this->confirmpassword);
        if (!$validconfirmpassword) {
            $this->errors['confirm_password'] = 'Mật khẩu không khớp';
        }

            return empty($this->errors);
        }

    public function count(): int
    {
        $statement = $this->db->prepare('select count(*) from user');
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function paginate(int $offset = 0, int $limit = 10): array
    {
        $users = [];
        $statement = $this->db->prepare('SELECT * FROM user LIMIT :offset, :limit');
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $user = new User($this->db);
            $user->fillFromDB($row);
            $users[] = $user;
    }
        return $users;
    }

    public function save(): bool
    {
        $result = false;

        if ($this->id >= 0) {
            $statement = $this->db->prepare(
            'update user set fullname = :fullname, phone_number = :phone_number, email = :email, password = :password, created_at = now(), updated_at = now(), address = :address, role = :role
            where id = :id'
        );
        $result = $statement->execute([
            'fullname' => $this->fullname,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'password' => $this->password,
            'id' => $this->id,
            'address' => $this->address,
            'role' => $this->role
        ]);
        } else {
            $statement = $this->db->prepare(
                'insert into user (fullname, phone_number, email, password, created_at, updated_at, address, role)
                values (:fullname, :phone_number, :email, :password, now(), now(), :address, :role)'
        );
        $result = $statement->execute([
            'fullname' => $this->fullname,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'password' => $this->password,
            'address' => $this->address,
            'role' => $this->role,
        ]);
        if ($result) {
            $this->id = $this->db->lastInsertId();
            }
        }
        return $result;
    }

    public function find(int $id): ?User
    {
        $statement = $this->db->prepare('select * from user where id = :id');
        $statement->execute(['id' => $id]);
        if ($row = $statement->fetch()) {
            $this->fillFromDB($row);
            return $this;
        }
        return null;
    }

    public function update(array $data): bool
{
    if (!empty($data['password'])) {
        $this->fill($data);
        if ($this->validate()) {
            return $this->save();
        }
    } else {
        $this->fillPersonalInfo($data);
        return $this->save();
    }

    return false;
}

protected function fillPersonalInfo(array $data): User
{
    $this->fullname = $data['fullname'] ?? $this->fullname;
    $this->phone_number = $data['phone_number'] ?? $this->phone_number;
    $this->email = $data['email'] ?? $this->email;
    $this->address = $data['address'] ?? $this->address;
    $this->role = $data['role'] ?? $this->role;
    
    return $this;
}


private function deleteAssociatedOrders(): void
{
    $statement = $this->db->prepare('DELETE FROM orders WHERE user_id = :user_id');
    $statement->execute(['user_id' => $this->id]);
}

private function hasAssociatedOrders(): bool
{
    $statement = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :user_id');
    $statement->execute(['user_id' => $this->id]);
    return $statement->fetchColumn() > 0;
}
public function delete(): bool
{
    try {
        $this->db->beginTransaction();

        if ($this->hasAssociatedOrders()) {
            return false;
        }

        $this->deleteAssociatedOrders();
        $statement = $this->db->prepare('DELETE FROM user WHERE id = :id');
        $result = $statement->execute(['id' => $this->id]);

        if ($result) {
            $this->db->commit();
        } else {
            $this->db->rollBack();
            echo "Error during user deletion: " . implode(" - ", $this->db->errorInfo());
        }

        return $result;
    } catch (PDOException $e) {
        $this->db->rollBack();
        echo "Exception during user deletion: " . $e->getMessage();
        throw $e;
    }
}


}