<?php

namespace ErikFig\ORM;

use ErikFig\ORM\Drivers\DriverStrategy;

abstract class Model
{
    protected $driver;

    public function setDriver(DriverStrategy $driver)
    {
        $this->driver = $driver;
        $this->driver->setTable($this->table);
        return $this;
    }

    protected function getDriver()
    {
        return $this->driver;
    }

    public function save()
    {
        $this->getDriver()
            ->save($this)
            ->exec();
    }

    public function findAll(array $conditions = [])
    {
        return $this->getDriver()
            ->select($conditions)
            ->exec()
            ->all();
    }

    public function findFirst($id)
    {
        return $this->getDriver()
            ->select(['id' => $id])
            ->exec()
            ->first();
    }

    public function delete()
    {
        $this->getDriver()
            ->delete(['id' => $this->id])
            ->exec();
    }

    public function __get($variable)
    {
        if ($variable === 'table') {
            $table = get_class($this);
            $table = explode('\\', $table);
            return strtolower(array_pop($table));
        }
        return null;
    }
}
