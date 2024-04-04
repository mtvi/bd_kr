<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VendorsRepository;
use App\Repository\GPURepository;
use App\Repository\ReviewsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ReviewType;
use App\Entity\Reviews;
use App\Repository\CategoriesRepository;
use App\Repository\ManufacturersRepository;
use App\Repository\MemoryTypesRepository;
use App\Repository\PCIRepository;
use ContainerLZNALkx\getReviewsTypeService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

class ProductsController extends AbstractController
{

    #[Route('/', name: 'products_list', methods: ['GET', 'HEAD'])]
    public function ProductList(ManufacturersRepository $manufacturersRepository, MemoryTypesRepository $memoryTypesRepository, PCIRepository $pCIRepository, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, VendorsRepository $vendorsRepository, GPURepository $gPURepository, Request $request)
    {
        $search = $request->query->get('search');
        $selectedGpus = $request->query->all()['gpus'] ?? null;
        $selectedVendors = $request->query->all()['vendors'] ?? null;
        $selectedManufacturers = $request->query->all()['manufacturers'] ?? null;
        $selectedMemories = $request->query->all()['memories'] ?? null;
        $selectedPCIVersions = $request->query->all()['pciversions'] ?? null;
        $selectedCategories = $request->query->all()['categories'] ?? null;
        $selectedMancountries = $request->query->all()['mancountries'] ?? null;
        $selectedVencountries = $request->query->all()['vencountries'] ?? null;
        $sort = $request->query->get('sort');
        $startPrice = $request->query->get('startPrice');
        $endPrice = $request->query->get('endPrice');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }


        $gpus = $gPURepository->findAll();
        $vendors = $vendorsRepository->findAll();
        $products = $productsRepository->search($search, $selectedGpus, $selectedVendors, $selectedManufacturers, $selectedMemories, $selectedPCIVersions, $selectedCategories, $selectedMancountries, $selectedVencountries, $startPrice, $endPrice, $sortBy, $sortDir);

        if ($sortBy != 'quantity') {
            $zeroQuantityProducts = [];
            $nonZeroQuantityProducts = [];

            foreach ($products as $product) {
                if ($product->getQuantity() == 0) {
                    $zeroQuantityProducts[] = $product;
                } else {
                    $nonZeroQuantityProducts[] = $product;
                }
            }

            // Merge the arrays
            $products = array_merge($nonZeroQuantityProducts, $zeroQuantityProducts);
        }
        $manufacturers = $manufacturersRepository->findAll();
        $memories = $memoryTypesRepository->findAll();
        $pciversions = $pCIRepository->findAll();
        $categories = $categoriesRepository->findAll();

        $mancountries = array_map(function ($manufacturer) {
            return $manufacturer->getCountry();
        }, $manufacturers);

        $vencountries = array_map(function ($vendor) {
            return $vendor->getCountry();
        }, $vendors);



        return $this->render('product/list.html.twig', [
            'products' => $products,
            'search' => $search,
            'sort' => $sort,
            'gpus' => $gpus,
            'vendors' => $vendors,
            'selectedGpus' => $selectedGpus,
            'selectedVendors' => $selectedVendors,
            'manufacturers' => $manufacturers,
            'memories' => $memories,
            'pciversions' => $pciversions,
            'categories' => $categories,
            'mancountries' => array_unique($mancountries),
            'vencountries' => array_unique($vencountries),
            'selectedManufacturers' => $selectedManufacturers,
            'selectedMemories' => $selectedMemories,
            'selectedPCIVersions' => $selectedPCIVersions,
            'selectedCategories' => $selectedCategories,
            'selectedManCountries' => $selectedMancountries,
            'selectedVenCountries' => $selectedVencountries,
            'startPrice' => $startPrice,
            'endPrice' => $endPrice,
        ]);
    }


    #[Route('/products/{id}}', name: 'product', methods: ['GET', 'POST'])]
    public function ProductInfo(ProductsRepository $productsRepository, $id, ReviewsRepository $reviewsRepository, Request $request, ManagerRegistry $doctrine)
    {
        $product = $productsRepository->findOneBy(['id' => $id]);
        $product->setView();
        $entityManager = $doctrine->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
        $sortDir = $request->query->get('sortDir', 'ASC');
        $reviews = $reviewsRepository->findByProductId($id, $sortDir);
        $review = new Reviews();

        $reviewForm = $this->createForm(ReviewType::class, $review);
        $reviewForm->handleRequest($request);

        // Check if the form is submitted and valid
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {

            $review->setProduct($product);
            $review->setReviewDate(new \DateTime());
            $avgReview = $review->getRating();
            foreach ($reviews as $rate) {
                $avgReview += $rate->getRating();
            }
            $avgReview /= count($reviews) + 1;
            $product->setRating($avgReview);
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('product', ['id' => $id]);
        }


        return $this->render('product/info.html.twig', [
            'reviews' => $reviews,
            'product' => $product,
            'reviewForm' => $reviewForm->createView(),
        ]);
    }
}
