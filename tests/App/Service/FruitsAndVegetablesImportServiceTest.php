<?php

namespace App\Tests\App\Service;

use App\Service\FruitsAndVegetablesImportService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class FruitsAndVegetablesImportServiceTest extends TestCase
{
    public function testReceivingRequest(): void
    {
        $requestString = file_get_contents('request.json');

        $em = $this->createMock(EntityManagerInterface::class);
        $importService = new FruitsAndVegetablesImportService($requestString, $em);

        $this->assertNotEmpty($importService->getRequest());
        $this->assertIsArray($importService->getRequest());
    }

    public function testProcessingRequest(): void
    {
        $requestString = file_get_contents('request.json');

        $em = $this->createMock(EntityManagerInterface::class);
        $importService = new FruitsAndVegetablesImportService($requestString, $em);
        $importService->processRequest();

        $this->assertSame(10, $importService->getVegetables()->count());
        $this->assertSame(10, $importService->getFruits()->count());
    }

    public function testReceivingVegetables(): void
    {
        $requestString = file_get_contents('request.json');

        $em = $this->createMock(EntityManagerInterface::class);
        $importService = new FruitsAndVegetablesImportService($requestString, $em);
        $importService->processRequest();

        $vegetables = $importService->getVegetables();

        $this->assertSame(10, $vegetables->count());
    }

    public function testReceivingFruits(): void
    {
        $requestString = file_get_contents('request.json');

        $em = $this->createMock(EntityManagerInterface::class);
        $importService = new FruitsAndVegetablesImportService($requestString, $em);
        $importService->processRequest();

        $fruits = $importService->getFruits();

        $this->assertSame(10, $fruits->count());
    }


}
