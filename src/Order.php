<?php

namespace CT275\Project;

use PDO;

class Order
{
    private ?PDO $db;

    private int $id = -1;
    public $user_id;
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    public $order_date;
    public $total_money;
    private array $errors = [];
    private array $orderDetails = [];
    private ?User $user = null;


    public function setUser(User $user)
    {
        $this->user = $user;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function __construct(?PDO $pdo)
    {
        if ($pdo !== null) {
            $this->db = $pdo;
        }
    }

    public function getUserInfo(): ?array
    {
        if ($this->user_id) {
            $statement = $this->db->prepare('SELECT * FROM user WHERE id = :user_id');
            $statement->execute([':user_id' => $this->user_id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function getOrderDetails(): array
    {
        $orderDetails = [];
        $statement = $this->db->prepare('SELECT * FROM order_details WHERE order_id = :order_id');
        $statement->execute([':order_id' => $this->getId()]);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $orderDetail = new OrderDetail($this->db);
            $orderDetail->fillFromDB($row);
            $orderDetails[] = $orderDetail;
        }

        return $orderDetails;
    }

    public function fill(array $data): Order
    {
        $this->user_id = $data['user_id'] ?? null;
        $this->fullname = $data['fullname'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_number = $data['phone_number'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->order_date = $data['order_date'] ?? '';
        $this->total_money = $data['total_money'] ?? '';
        return $this;
    }

    public function all(): array
    {
        $orders = [];
        $statement = $this->db->prepare('SELECT * FROM orders');
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($this->db);
            $order->fillFromDB($row);
            $orders[] = $order;
        }

        return $orders;
    }

    protected function fillFromDB(array $row): Order
    {
        [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'order_date' => $this->order_date,
            'total_money' => $this->total_money
        ] = $row;
        return $this;
    }
    public function getAllOrders()
    {
        $statement = $this->db->prepare('SELECT * FROM orders');
        $statement->execute();

        $orders = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($this->db);
            $order->fillFromDB($row);
            $orderDetails = $order->getOrderDetails();
            $order->setOrderDetails($orderDetails);

            $orders[] = $order;
        }

        return $orders;
    }

public function setOrderDetails(array $orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }
    public function getValidationErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        return empty($this->errors);
    }

    public function count(): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM orders');
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function paginateByUserId(int $userId, int $offset = 0, int $limit = 10): array
    {
        $orders = [];
        $statement = $this->db->prepare('
            SELECT * FROM orders
            WHERE user_id = :user_id
            ORDER BY order_date DESC
            LIMIT :offset, :limit
        ');

        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($this->db);
            $order->fillFromDB($row);
            $orders[] = $order;
        }

        return $orders;
    }

    public function paginateByOrderDate($offset, $limit)
{
    $statement = $this->db->prepare('
        SELECT o.*, u.fullname AS user_fullname, u.phone_number AS user_phone_number, u.address AS user_address
        FROM orders o
        JOIN user u ON o.user_id = u.id
        WHERE u.role = 0
        ORDER BY o.order_date DESC
        LIMIT :offset, :limit
    ');

    $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
    $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);

    $statement->execute();

    $orders = [];
    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        $order = new Order($this->db);

        // Populate Order properties
        $order->fillFromDB($row);

        // Populate User properties
        $user = new User();
        $user->setId($row['user_id']);
        $user->setFullname($row['user_fullname']);
        $user->setPhoneNumber($row['user_phone_number']);
        $user->setAddress($row['user_address']);
        $order->setUser($user);

        // Fetch and set OrderDetails
        $orderDetails = $order->getOrderDetails();
        $order->setOrderDetails($orderDetails);

        $orders[] = $order;
    }

    return $orders;
}


    public function save(): bool
    {
        $result = false;
        $user_info = $this->getUserInfo();
        if ($this->id >= 0) {

            $statement = $this->db->prepare(
                'UPDATE orders 
                    SET user_id = :user_id,
                    fullname = :fullname, 
                    email = :email, 
                    phone_number = :phone_number,
                    address = :address, 
                    order_date = :order_date,
                    total_money = :total_money
                WHERE id = :id'
            );

            $result = $statement->execute([
                'user_id' => $this->user_id,
                'fullname' => $user_info['fullname'],
                'email' => $user_info['email'],
                'phone_number' => $user_info['phone_number'],
                'address' => $user_info['address'],
                'order_date' => $this->order_date,
                'total_money' => $this->total_money,
                'id' => $this->id,
            ]);
        } else {

            $statement = $this->db->prepare(
                'INSERT INTO orders 
                (user_id, fullname, email, phone_number, address, order_date, total_money)
                VALUES (:user_id, :fullname, :email, :phone_number, :address, :order_date, :total_money)'
            );
            
            $result = $statement->execute([
                'user_id' => $this->user_id,
                'fullname' => $user_info['fullname'],
                'email' => $user_info['email'],
                'phone_number' => $user_info['phone_number'],
                'address' => $user_info['address'],
                'order_date' => $this->order_date,
                'total_money' => $this->total_money,
            ]);            

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }

        return $result;
    }

    public function find(int $id): ?Order
    {
        $statement = $this->db->prepare('SELECT * FROM orders WHERE id = :id');
        $statement->execute(['id' => $id]);
        if ($row = $statement->fetch()) {
            $this->fillFromDB($row);
            return $this;
        }
        return null;
    }

    public function update(array $data): bool
    {
        $this->fill($data);
        if ($this->validate()) {
            return $this->save();
        }
        return false;
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare('DELETE FROM orders WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

    public function getTotalMoney(): float
    {
        $totalMoney = 0;

        foreach ($this->getOrderDetails() as $orderDetail) {
            $totalMoney += $orderDetail->total_money;
        }

        return $totalMoney;
    }

}
