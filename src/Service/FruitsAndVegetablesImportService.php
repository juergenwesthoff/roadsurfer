<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use Doctrine\Common\Collections\ArrayCollection;

class FruitsAndVegetablesImportService
{
    const string FRUIT = 'fruit';
    const string VEGETABLE = 'vegetable';

    const string UNIT_KILOGRAMS = 'kg';
    const string UNIT_GRAMS = 'g';

    protected array $request;
    protected ArrayCollection $vegetables;
    protected ArrayCollection $fruits;

    /**
     * @InjectParams
     */
    public function __construct(
        string $request
    )
    {
        $this->request = json_decode($request, true);
        $this->vegetables = new ArrayCollection();
        $this->fruits = new ArrayCollection();
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function processRequest(): array
    {
        foreach ($this->request as $row) {
            $this->addElement($row);
        }

        return [
            self::FRUIT => $this->fruits,
            self::VEGETABLE => $this->vegetables
        ];

    }

    protected function convertToGram($row): array
    {
        if ($row['unit'] === self::UNIT_KILOGRAMS) {
            $row['unit'] = self::UNIT_GRAMS;
            $row['quantity'] = $row['quantity'] * 1000;
        }

        return $row;
    }

    public function getVegetables(): ArrayCollection
    {
        return $this->vegetables;
    }

    public function getFruits(): ArrayCollection
    {
        return $this->fruits;
    }

    public function addElement($row): void
    {
        $row = $this->convertToGram($row);

        if ($row['type'] === self::VEGETABLE) {
            $vegetable = new Vegetable(
                $row['id'],
                $row['name'],
                $row['quantity'],
                $row['unit']
            );
            $this->vegetables->add($vegetable);
        }
        if ($row['type'] === self::FRUIT) {
            $fruit = new Fruit(
                $row['id'],
                $row['name'],
                $row['quantity'],
                $row['unit']
            );
            $this->fruits->add($fruit);
        }
    }

    public function removeElement($row): void
    {
        if ($row['type'] === self::VEGETABLE) {
            $this->vegetables->removeElement($this->convertToGram($row));
        }
        if ($row['type'] === self::FRUIT) {
            $this->fruits->removeElement($this->convertToGram($row));
        }
    }
}
