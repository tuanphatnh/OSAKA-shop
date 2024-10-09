<?php

namespace CT275\Project;

use PDO;

class OrderDetail
{
    private ?PDO $db;

    private int $id = -1;
    public $order_id;
    public $product_id;
    public $price;
    public $num;
    public $total_money;
    private array $errors = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function __construct(?PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function fill(array $data): OrderDetail
    {
        $this->order_id = $data['order_id'] ?? null;
        $this->product_id = $data['product_id'] ?? null;
        $this->price = $data['price'] ?? 0;
        $this->num = $data['num'] ?? 0;
        $this->total_money = $data['total_money'] ?? 0;
        return $this;
    }

    public function allByOrderId(int $orderId): array
    {
        $orderDetails = [];
        $statement = $this->db->prepare('SELECT * FROM order_details WHERE order_id = :order_id');
        $statement->execute([':order_id' => $orderId]);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $orderDetail = new OrderDetail($this->db);
            $orderDetail->fillFromDB($row);
            $orderDetails[] = $orderDetail;
        }

        return $orderDetails;
    }
    public function getProductName(): ?string
    {
        $statement = $this->db->prepare('SELECT title FROM product WHERE id = :product_id');
        $statement->execute([':product_id' => $this->product_id]);

        return $statement->fetchColumn();
    }
    public function fillFromDB(array $row): OrderDetail
    {
        [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'num' => $this->num,
            'total_money' => $this->total_money
        ] = $row;
        return $this;
    }

    public function getValidationErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        return empty($this->errors);
    }

    public function save(): bool
    {
        $result = false;

        if ($this->id >= 0) {
            $statement = $this->db->prepare(
                'UPDATE order_details 
                    SET order_id = :order_id,
                    product_id = :product_id, 
                    price = :price, 
                    num = :num,
                    total_money = :total_money
                WHERE id = :id'
            );

            $result = $statement->execute([
                'order_id' => $this->order_id,
                'product_id' => $this->product_id,
                'price' => $this->price,
                'num' => $this->num,
                'total_money' => $this->total_money,
                'id' => $this->id,
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO order_details 
                (order_id, product_id, price, num, total_money)
                VALUES (:order_id, :product_id, :price, :num, :total_money)'
            );

            $result = $statement->execute([
                'order_id' => $this->order_id,
                'product_id' => $this->product_id,
                'price' => $this->price,
                'num' => $this->num,
                'total_money' => $this->total_money,
            ]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }

        return $result;
    }

    public function findByOrderIdAndProductId(int $orderId, int $productId): ?OrderDetail
    {
        $statement = $this->db->prepare('SELECT * FROM order_details WHERE order_id = :order_id AND product_id = :product_id');
        $statement->execute([':order_id' => $orderId, ':product_id' => $productId]);

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
        $statement = $this->db->prepare('DELETE FROM order_details WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }
}