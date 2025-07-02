<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Service\FruitsAndVegetablesImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FruitsAndVegetablesController extends AbstractController
{
    #[Route('/process', name: 'app_process')]
    public function process(EntityManagerInterface $em): JsonResponse
    {
        $requestString = file_get_contents('../request.json');
        $importService = new FruitsAndVegetablesImportService($requestString);
        $result = $importService->processRequest();
        $fruits = $result[FruitsAndVegetablesImportService::FRUIT];
        $vegetables = $result[FruitsAndVegetablesImportService::VEGETABLE];

        foreach ($fruits as $fruit) {
            $em->persist($fruit);
        }
        foreach ($vegetables as $vegetable) {
            $em->persist($vegetable);
        }

        $em->flush();

        return $this->json(sprintf('%s fruits and %s vegetables processed and saved.',
            $fruits->count(),
            $vegetables->count()
        ));
    }

    #[Route('/fruits', name: 'app_fruits', methods: ['GET'])]
    public function fruits(EntityManagerInterface $em): JsonResponse
    {
        $fruits = $em->getRepository(Fruit::class)->findAll();

        return $this->json($fruits);
    }

    #[Route('/vegetables', name: 'app_vegetables', methods: ['GET'])]
    public function vegetables(EntityManagerInterface $em): JsonResponse
    {
        $vegetables = $em->getRepository(Vegetable::class)->findAll();

        return $this->json($vegetables);
    }

    #[Route('/fruits_and_vegetables', name: 'app_fruits_and_vegetables')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $fruits = $em->getRepository(Fruit::class)->findAll();
        $vegetables = $em->getRepository(Vegetable::class)->findAll();

        return $this->json([
            FruitsAndVegetablesImportService::FRUIT => $fruits,
            FruitsAndVegetablesImportService::VEGETABLE => $vegetables,
        ]);
    }

    #[Route('/vegetables', name: 'app_add_vegetable', methods: ['POST'])]
    public function addVegetable(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $vegetable = new Vegetable(
            null,
            $request->get('name'),
            (int) $request->get('quantity'),
            FruitsAndVegetablesImportService::UNIT_GRAMS
        );
        $em->persist($vegetable);
        $em->flush();

        return $this->json('vegetable stored.');
    }

    #[Route('/fruits', name: 'app_add_fruit', methods: ['POST'])]
    public function addfruit(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $fruit = new Fruit(
            null,
            $request->get('name'),
            (int) $request->get('quantity'),
            FruitsAndVegetablesImportService::UNIT_GRAMS
        );
        $em->persist($fruit);
        $em->flush();

        return $this->json('fruit stored.');
    }

}
