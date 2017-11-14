<?php

$entityManager = EntityManager::create($conn, $config);

class Product
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}

$product = new Product();
$product->setName('Erik');

$entityManager->persist($product);
$entityManager->flush();
