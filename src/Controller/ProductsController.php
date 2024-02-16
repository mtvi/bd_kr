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
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }


        $gpus = $gPURepository->findAll();
        $vendors = $vendorsRepository->findAll();
        $products = $productsRepository->search($search, $selectedGpus, $selectedVendors, $selectedManufacturers, $selectedMemories, $selectedPCIVersions, $selectedCategories, $selectedMancountries, $selectedVencountries, $sortBy, $sortDir);

        $manufacturers = $manufacturersRepository->findAll();
        $memories = $memoryTypesRepository->findAll();
        $pciversions = $pCIRepository->findAll();
        $categories = $categoriesRepository->findAll();

        $mancountries = array_map(function($manufacturer) {
            return $manufacturer->getCountry(); // Replace 'getCountry()' with the actual getter method
        }, $manufacturers);

        $vencountries = array_map(function($vendor) {
            return $vendor->getCountry(); // Replace 'getCountry()' with the actual getter method
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
            'mancountries' => $mancountries,
            'vencountries' => $vencountries,
            'selectedManufacturers' => $selectedManufacturers,
            'selectedMemories' => $selectedMemories,
            'selectedPCIVersions' => $selectedPCIVersions,
            'selectedCategories' => $selectedCategories,
            'selectedManCountries' => $selectedMancountries,
            'selectedVenCountries' => $selectedVencountries,
        ]);
    }


    #[Route('/products/{id}}', name: 'product', methods: ['GET', 'POST'])]
    public function ProductInfo(ProductsRepository $productsRepository, $id, ReviewsRepository $reviewsRepository, Request $request, ManagerRegistry $doctrine)
    {
        $product = $productsRepository->findOneBy(['id' => $id]);
        $sortDir = $request->query->get('sortDir', 'ASC');
        $reviews = $reviewsRepository->findByProductId($id, $sortDir);
        $review = new Reviews();
        $reviewForm = $this->createForm(ReviewType::class, $review);
        $reviewForm->handleRequest($request);

        // Check if the form is submitted and valid
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {

            $review->setProduct($product);
            $review->setReviewDate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            // Optionally add a flash message or other action

            // Redirect to avoid form resubmission
            return $this->redirectToRoute('product', ['id' => $id]);
        }


        return $this->render('product/info.html.twig', [
            'reviews' => $reviews,
            'product' => $product,
            'reviewForm' => $reviewForm->createView(),
        ]);
    }
}
