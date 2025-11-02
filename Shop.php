<?php

require_once __DIR__ . '/BaseModel.php';

class Shop extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'shop');
    }
}
