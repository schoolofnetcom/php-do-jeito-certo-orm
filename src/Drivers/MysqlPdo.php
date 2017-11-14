<?php

namespace ErikFig\ORM\Drivers;

use ErikFig\ORM\Model;

class MysqlPdo implements DriverStrategy
{
    protected $pdo;
    protected $table;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function save(Model $data)
    {
        if (!empty($data->id)) {
            $this->update($data);
            return $this;
        }
        $this->insert($data);

        return $this;
    }

    public function insert(Model $data)
    {
        $query = 'INSERT INTO %s (%s) VALUES (%s)';

        $fields = [];
        $fields_to_bind = [];

        foreach ($data as $field => $value) {
            $fields[] = $field;
            $fields_to_bind[] = ':' . $field;
        }

        $fields = implode(', ', $fields);
        $fields_to_bind = implode(', ', $fields_to_bind);

        $query = sprintf($query, $this->table, $fields, $fields_to_bind);

        $this->query = $this->pdo->prepare($query);

        $this->bind($data);

        return $this;
    }

    public function update(Model $data)
    {
        if (empty($data->id)) {
            throw new \Exception("Id is required");
        }

        $query = 'UPDATE %s SET %s';

        $data_to_update = $this->params($data);

        $query = sprintf($query, $this->table, $data_to_update);
        $query .= ' WHERE id=:id';

        $this->query = $this->pdo->prepare($query);
        $this->bind($data);

        return $this;
    }

    public function select(array $conditions = [])
    {
        $query = 'SELECT * FROM ' . $this->table;

        $data = $this->params($conditions);

        if ($data) {
            $query .= ' WHERE ' . $data;
        }

        $this->query = $this->pdo->prepare($query);

        $this->bind($conditions);

        return $this;
    }

    public function delete(array $conditions)
    {
        $query = 'DELETE FROM ' . $this->table;

        $data = $this->params($conditions);
        $query .= ' WHERE ' . $data;

        $this->query = $this->pdo->prepare($query);

        $this->bind($conditions);

        return $this;
    }

    public function exec(string $query = null)
    {
        if ($query) {
            $this->query = $this->pdo->prepare($query);
        }
        $this->query->execute();
        return $this;
    }

    public function first()
    {
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    public function all()
    {
        return $this->query->fetchaLL(\PDO::FETCH_ASSOC);
    }

    protected function params($conditions)
    {
        $fields = [];
        foreach ($conditions as $field => $value) {
            $fields[] = $field. '=:' . $field;
        }

        return implode(', ', $fields);
    }

    protected function bind($data)
    {
        foreach ($data as $field => $value) {
            $this->query->bindValue($field, $value);
        }
    }
}
