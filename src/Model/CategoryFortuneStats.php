<?php

namespace App\Model;

class CategoryFortuneStats
{
    public function __construct(
        public int $numberPrinted,
        public float $averagePrinted,
        public string $categoryName
    )
    {
    }  
}