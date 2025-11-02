<?php

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product');
    }
}
