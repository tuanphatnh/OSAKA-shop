<?php

namespace CT275\Project;

use PDO;

class Product
{
    private ?PDO $db;

    private int $id = -1;
    public $title;
    public $price;
    public $thumbnail;
    public $description;
    public $created_at;
    public $updated_at;
    public $deleted;
    private array $errors = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function __construct(?PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function fill(array $data): Product
    {
        $this->title = $data['title'] ?? '';
        $this->price = $data['price'] ?? '';
        $this->thumbnail = $data['thumbnail'] ?? '';
        $this->description = $data['description'] ?? '';
        return $this;
    }

    public function all(): array
        {
            $products = [];
            $product = ['name' => 'Sản phẩm 1', 'price' => 100000];
            $statement = $this->db->prepare('select * from product');
            $statement->execute();
            while ($row = $statement->fetch()) {
            $products = new Product($this->db);
            $products->fillFromDB($row);
            $products[] = $product;
            }
            return $products;
        }
    protected function fillFromDB(array $row): Product
        {
            [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'thumbnail' => $this->thumbnail,
            'description' => $this->description,
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
        $title = trim($this->title);
        if (!$title) {
            $this->errors['tittle'] = 'Invalid title.';
        }

        $validPrice = preg_match(
            '/^([0-9]{1,}(?:[.,][0-9]{0})?)$/',
            $this->price
        );
        if (!$validPrice) {
            $this->errors['price'] = 'Invalid price.';
        }

        $description = trim($this->description);
        if (strlen($description) > 255) {
            $this->errors['description'] = 'Description must be at most 255 characters.';
        }

        return empty($this->errors);
    }

    public function count(): int
    {
        $statement = $this->db->prepare('select count(*) from product');
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function paginate(int $offset = 0, int $limit = 10): array
    {
        $products = [];
        $statement = $this->db->prepare('select * from product limit :offset,
        :limit');
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $product = new Product($this->db);
            $product->fillFromDB($row);
            $products[] = $product;
    }
    return $products;
    }

    public function save(): bool
    {
        $result = false;

        if ($this->id >= 0) {
            $statement = $this->db->prepare(
            'update product set title = :title,
            price = :price, description = :description, updated_at = now(), thumbnail = :thumbnail
            where id = :id'
        );
        $result = $statement->execute([
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'id' => $this->id,
            'thumbnail' => $this->thumbnail,
        ]);
        } else {
            $statement = $this->db->prepare(
                'insert into product (title, price, description, created_at, updated_at, thumbnail)
                values (:title, :price, :description, now(), now(), :thumbnail)'
        );
        $result = $statement->execute([
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail
        ]);
        if ($result) {
            $this->id = $this->db->lastInsertId();
            }
        }
        return $result;
    }

    public function find(int $id): ?Product
    {
        $statement = $this->db->prepare('select * from product where id = :id');
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
        if (isset($data['thumbnail']) && $data['thumbnail'] !== '') {
            $this->thumbnail = $data['thumbnail'];
        }
        if ($this->validate()) {
            return $this->save();
        }
        return false;
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare('delete from product where id = :id');
        return $statement->execute(['id' => $this->id]);
    }
}
