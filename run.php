<?php

// configuração inicial
require __DIR__ . '/vendor/autoload.php';

use App\Model\Users;
use ErikFig\ORM\Drivers\MysqlPdo;

// conexão ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=curso_orm_php', 'root', '1234');

// instanciação da classe de sql (driver)
$driver = new MysqlPdo($pdo);

// exemplo de execução com o driver
$driver->exec('truncate users;');

// instanciação do model
$model = new Users;
$model->setDriver($driver);

// inserção de registros
$model->name = 'Erik';
$model->age = 32;
$model->email = 'e@e.com';
$model->save();

$model->name = 'Outro';
$model->age = 25;
$model->email = 'o@o.com';
$model->save();

// busca de vários registros
var_dump($model->findAll());

// busca de um registro
var_dump($model->findFirst(1));

// Atualização de um registro
$model->id = 2;
$model->name = 'José';
$model->save();

var_dump($model->findFirst(2));

// remoção de um registro
$model->id = 2;
$model->delete();
var_dump($model->findAll());
