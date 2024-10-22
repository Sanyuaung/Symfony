<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductImportType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CsvImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;

#[Route('/')]
final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        // Get all products from the repository
        $products = $productRepository->findBy([], ['createdDatetime' => 'DESC']);

        // Render the view with products
        return $this->render('product/index.html.twig', [
            'products' => $products, // Pass all products to the view
        ]);
    }


    #[Route('/s', name: 'app_product_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        // Initialize search parameters with GET parameters
        $searchParams = [
            'name' => $request->query->get('name'),
            'priceRange' => [
                $request->query->get('min_price'),
                $request->query->get('max_price'),
            ],
            'stockQuantity' => $request->query->get('stock_quantity'),
            'createdDatetime' => $request->query->get('created_datetime'),
        ];

        // Get products based on search parameters without pagination
        $products = $productRepository->findAllWithFilters($searchParams);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'searchParams' => $searchParams,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully!');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        // Add flash message only when the form is submitted but not valid
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'There are errors in your form. Please fix them and try again.');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Product update successfully!');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Product deleted successfully!');

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/product/import', name: 'app_product_import', methods: ['GET', 'POST'])]
    public function import(Request $request, CsvImportService $csvImportService): Response
    {
        $form = $this->createForm(ProductImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                $csvImportService->importFromCsv($file);
                $this->addFlash('success', 'Products imported successfully!');
                return $this->redirectToRoute('app_product_index');
            }
        }

        return $this->render('product/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}