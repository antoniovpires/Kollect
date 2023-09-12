<?php

class ProductGateway
{
    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM product";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row["is_available"] = (bool) $row["is_available"];

            $data[] = $row;

        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO product (name, description, year, is_available) VALUES (:name, :description, :year, :is_available)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(":year", $data['year'], PDO::PARAM_INT);
        $stmt->bindValue(":is_available", (bool) ($data['is_available'] ?? FALSE), PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array
    {
        $sql = "SELECT * FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["is_available"] = (bool) $data["is_available"];
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE product
                SET name = :name, description = :description, year = :year, is_available = :is_available
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $new["description"] ?? $current["description"], PDO::PARAM_STR);
        $stmt->bindValue(":year", $new["year"] ?? $current["year"], PDO::PARAM_INT);
        $stmt->bindValue(":is_available", $new["is_available"] ?? $current["is_available"], PDO::PARAM_BOOL);

        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}