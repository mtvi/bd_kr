<?php

namespace App\Controller;

use App\Entity\GPU;
use App\Entity\PCI;
use App\Form\GPUType;
use App\Form\PCIType;
use App\Entity\Orders;
use App\Entity\Reviews;
use App\Entity\Vendors;
use App\Form\OrderType;
use App\Entity\Products;
use App\Form\MemoryType;
use App\Form\VendorType;
use App\Form\ReviewsType;
use App\Entity\Categories;
use App\Form\CategoryType;
use App\Entity\MemoryTypes;
use App\Entity\OrderDetails;
use App\Entity\Manufacturers;
use App\Form\ProductFormType;
use App\Form\ManufacturerType;
use App\Form\OrderDetailsType;
use App\Repository\CategoriesRepository;
use App\Repository\GPURepository;
use App\Repository\ManufacturersRepository;
use App\Repository\MemoryTypesRepository;
use App\Repository\OrderDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\PCIRepository;
use App\Repository\ProductsRepository;
use App\Repository\ReviewsRepository;
use App\Repository\VendorsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * Create a new entity based on form type and template.
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param string $entityClass
     * @param string $formTypeClass
     * @param string $template
     * @return Response
     */
    private function createEntity(
        Request $request,
        ManagerRegistry $doctrine,
        string $entityClass,
        string $formTypeClass,
        string $template,
        string $redirectRoute = 'app_admin' // default redirect route
    ): Response {
        $entity = new $entityClass();
        $form = $this->createForm($formTypeClass, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image file upload only if the entity has the 'image' property
            if (method_exists($entity, 'getImage')) {
                $redirectRoute = 'products_view';
                $uploadedFile = $form->get('image')->getData();

                if ($uploadedFile) {
                    // Generate a unique name for the file
                    $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

                    // Move the file to the directory where images are stored
                    $uploadedFile->move('images', $newFilename);

                    // Update the 'image' property of the entity
                    $entity->setImage($newFilename);
                }
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/products/create', name: 'create_product')]
    public function createProduct(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Products::class, ProductFormType::class, 'admin/create_form/create_product.html.twig');
    }

    #[Route('/admin/vendor/create', name: 'create_vendor')]
    public function createVendor(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Vendors::class, VendorType::class, 'admin/create_form/create_vendor.html.twig');
    }

    #[Route('/admin/gpu/create', name: 'create_gpu')]
    public function createGPU(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, GPU::class, GPUType::class, 'admin/create_form/create_gpu.html.twig');
    }

    #[Route('/admin/review/create', name: 'create_review')]
    public function createReview(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Reviews::class, ReviewsType::class, 'admin/create_form/create_review.html.twig');
    }

    #[Route('/admin/category/create', name: 'create_category')]
    public function createCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Categories::class, CategoryType::class, 'admin/create_form/create_category.html.twig');
    }
    #[Route('/admin/manufacturer/create', name: 'create_manufacturer')]
    public function createManufacturer(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Manufacturers::class, ManufacturerType::class, 'admin/create_form/create_manufacturer.html.twig');
    }
    #[Route('/admin/memorytype/create', name: 'create_memorytype')]
    public function createMemoryType(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, MemoryTypes::class, MemoryType::class, 'admin/create_form/create_memorytype.html.twig');
    }
    #[Route('/admin/orderdetails/create', name: 'create_orderdetails')]
    public function createOrderDetails(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, OrderDetails::class, OrderDetailsType::class, 'admin/create_form/create_orderdetails.html.twig');
    }
    #[Route('/admin/order/create', name: 'create_order')]
    public function createOrder(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, Orders::class, OrderType::class, 'admin/create_form/create_order.html.twig');
    }
    #[Route('/admin/pci/create', name: 'create_pci')]
    public function createPCI(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->createEntity($request, $doctrine, PCI::class, PCIType::class, 'admin/create_form/create_pci.html.twig');
    }




    // ================Products================
    #[Route('/admin/products', name: 'products_view', methods: ['GET', 'HEAD'])]
    public function viewProduct(ProductsRepository $productsRepository, VendorsRepository $vendorsRepository, GPURepository $gPURepository, Request $request)
    {

        $search = $request->query->get('search');
        $selectedGpu = $request->query->get('gpu');
        $selectedVendor = $request->query->get('vendor');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }


        $gpus = $gPURepository->findAll();
        $vendors = $vendorsRepository->findAll();
        $products = $productsRepository->search($search, $selectedGpu, $selectedVendor, $sortBy, $sortDir);


        return $this->render('admin/view/products.html.twig', [
            'products' => $products,
            'search' => $search,
            'sort' => $sort,
            'gpus' => $gpus,
            'vendors' => $vendors,
            'selectedGpu' => $selectedGpu,
            'selectedVendor' => $selectedVendor,
        ]);
    }
    #[Route('/admin/products/delete/{id}', name: 'delete_product')]
    public function deleteProduct(ProductsRepository $productsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $product = $productsRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Remove the product
        $doctrine->getManager()->remove($product);
        $doctrine->getManager()->flush();

        // Redirect to a success page or do something else
        return $this->redirectToRoute('products_view');
    }
    #[Route('/admin/products/edit/{id}', name: 'edit_product')]
    public function editProduct(Request $request, ProductsRepository $productsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $productsRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('products_view');
        }

        return $this->render('admin/create_form/create_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    // ================Vendor================
    #[Route('/admin/vendors', name: 'vendors_view', methods: ['GET', 'HEAD'])]
    public function viewVendor(VendorsRepository $vendorsRepository, Request $request)
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $vendors = $vendorsRepository->search($search, $sortBy, $sortDir);
        return $this->render('admin/view/vendors.html.twig', [
            'vendors' => $vendors,
            'search' => $search,
            'sort' => $sort
        ]);
    }
    #[Route('/admin/vendors/delete/{id}', name: 'delete_vendor')]
    public function deleteVendor(VendorsRepository $vendorsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $vendor = $vendorsRepository->find($id);

        if (!$vendor) {
            throw $this->createNotFoundException('Vendor not found');
        }

        $doctrine->getManager()->remove($vendor);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('vendors_view');
    }
    #[Route('/admin/vendors/edit/{id}', name: 'edit_vendor')]
    public function editVendor(Request $request, VendorsRepository $vendorsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $vendor = $vendorsRepository->find($id);

        if (!$vendor) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(VendorType::class, $vendor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('vendors_view');
        }

        return $this->render('admin/create_form/create_vendor.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    // ================GPU================
    #[Route('/admin/gpus', name: 'gpus_view', methods: ['GET', 'HEAD'])]
    public function viewGPU(GPURepository $gPURepository, ManufacturersRepository $manufacturersRepository, MemoryTypesRepository $memoryTypesRepository, PCIRepository $pCIRepository, CategoriesRepository $categoriesRepository, Request $request)
    {
        $search = $request->query->get('search');
        $manufacturer = $request->query->get('manufacturer');
        $memory = $request->query->get('memory');
        $pci = $request->query->get('pci');
        $category = $request->query->get('category');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $manufacturers = $manufacturersRepository->findAll();
        $memoryTypes = $memoryTypesRepository->findAll();
        $pcis = $pCIRepository->findAll();
        $categories = $categoriesRepository->findAll();

        $gpus = $gPURepository->search($search, $manufacturer, $memory, $pci, $category, $sortBy, $sortDir);

        return $this->render('admin/view/gpus.html.twig', [
            'gpus' => $gpus,
            'search' => $search,
            'sort' => $sort,
            'Smanufacturer' => $manufacturer,
            'manufacturers' => $manufacturers,
            'Spci'=> $pci,
            'pcis'=> $pcis,
            'Scategory'=> $category, 
            'categories'=> $categories,
            'memoryTypes'=> $memoryTypes,
            'Smemory'=> $memory,
            
        ]);
    }
    #[Route('/admin/gpus/delete/{id}', name: 'delete_gpu')]
    public function deleteGPU(GPURepository $gPURepository, ManagerRegistry $doctrine, $id): Response
    {
        $vendor = $gPURepository->find($id);

        if (!$vendor) {
            throw $this->createNotFoundException('GPU not found');
        }

        $doctrine->getManager()->remove($vendor);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('gpus_view');
    }
    #[Route('/admin/gpus/edit/{id}', name: 'edit_gpu')]
    public function editGPU(Request $request, GPURepository $gPURepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $gpu = $gPURepository->find($id);

        if (!$gpu) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(GPUType::class, $gpu);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('gpus_view');
        }

        return $this->render('admin/create_form/create_gpu.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // ================Manufacturers================
    #[Route('/admin/manufacturers', name: 'manufacturers_view', methods: ['GET', 'HEAD'])]
    public function viewManufacturers(ManufacturersRepository $manufacturersRepository, Request $request)
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $manufacturers = $manufacturersRepository->search($search, $sortBy, $sortDir);
        return $this->render('admin/view/manufacturers.html.twig', [
            'manufacturers' => $manufacturers,
            'search' => $search,
            'sort' => $sort
        ]);
    }
    #[Route('/admin/manufacturers/delete/{id}', name: 'delete_manufacturer')]
    public function deleteManufacturers(ManufacturersRepository $manufacturersRepository, ManagerRegistry $doctrine, $id): Response
    {
        $manufacturer = $manufacturersRepository->find($id);

        if (!$manufacturer) {
            throw $this->createNotFoundException('Manufacturer not found');
        }

        $doctrine->getManager()->remove($manufacturer);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('manufacturers_view');
    }
    #[Route('/admin/manufacturers/edit/{id}', name: 'edit_manufacturer')]
    public function editManufacturers(Request $request, ManufacturersRepository $manufacturersRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $manufacturer = $manufacturersRepository->find($id);

        if (!$manufacturer) {
            throw $this->createNotFoundException('Manufacturer not found');
        }

        $form = $this->createForm(ManufacturerType::class, $manufacturer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('manufacturers_view');
        }

        return $this->render('admin/create_form/create_manufacturer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ================Categories================
    #[Route('/admin/categories', name: 'categories_view', methods: ['GET', 'HEAD'])]
    public function viewCategories(CategoriesRepository $categoriesRepository, Request $request)
    {
        $search = $request->query->get('search');


        $categories = $categoriesRepository->search($search);
        return $this->render('admin/view/categories.html.twig', [
            'categories' => $categories,
            'search' => $search
        ]);
    }
    #[Route('/admin/categories/delete/{id}', name: 'delete_category')]
    public function deleteCategories(CategoriesRepository $categoriesRepository, ManagerRegistry $doctrine, $id): Response
    {
        $categories = $categoriesRepository->find($id);

        if (!$categories) {
            throw $this->createNotFoundException('category not found');
        }

        $doctrine->getManager()->remove($categories);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('categories_view');
    }

    #[Route('/admin/categories/edit/{id}', name: 'edit_category')]
    public function editCategories(Request $request, CategoriesRepository $categoriesRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $categories = $categoriesRepository->find($id);

        if (!$categories) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryType::class, $categories);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('categories_view');
        }

        return $this->render('admin/create_form/create_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // ================Memory================
    #[Route('/admin/memories', name: 'memories_view', methods: ['GET', 'HEAD'])]
    public function viewMemories(MemoryTypesRepository $memoryTypesRepository, Request $request)
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $memories = $memoryTypesRepository->search($search, $sortBy, $sortDir);
        return $this->render('admin/view/memories.html.twig', [
            'memories' => $memories,
            'search' => $search,
            'sort' => $sort
        ]);
    }
    #[Route('/admin/memories/delete/{id}', name: 'delete_memory')]
    public function deleteMemory(MemoryTypesRepository $memoryTypesRepository, ManagerRegistry $doctrine, $id): Response
    {
        $memories = $memoryTypesRepository->find($id);

        if (!$memories) {
            throw $this->createNotFoundException('Memory not found');
        }

        $doctrine->getManager()->remove($memories);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('memories_view');
    }
    #[Route('/admin/memories/edit/{id}', name: 'edit_memory')]
    public function editMemory(Request $request, MemoryTypesRepository $memoryTypesRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $memories = $memoryTypesRepository->find($id);

        if (!$memories) {
            throw $this->createNotFoundException('Memory not found');
        }

        $form = $this->createForm(MemoryType::class, $memories);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('memories_view');
        }

        return $this->render('admin/create_form/create_memory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ================Memory================
    #[Route('/admin/pcies', name: 'pcies_view', methods: ['GET', 'HEAD'])]
    public function viewPcies(PCIRepository $pCIRepository, Request $request)
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $pcies = $pCIRepository->search($search, $sortBy, $sortDir);
        return $this->render('admin/view/pcies.html.twig', [
            'pcies' => $pcies,
            'search' => $search,
            'sort' => $sort
        ]);
    }
    #[Route('/admin/pcies/delete/{id}', name: 'delete_pci')]
    public function deletePCI(PCIRepository $pCIRepository, ManagerRegistry $doctrine, $id): Response
    {
        $pci = $pCIRepository->find($id);

        if (!$pci) {
            throw $this->createNotFoundException('PCI not found');
        }

        $doctrine->getManager()->remove($pci);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('pcies_view');
    }
    #[Route('/admin/pcies/edit/{id}', name: 'edit_pci')]
    public function edityPCI(Request $request, PCIRepository $pCIRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $pcies = $pCIRepository->find($id);

        if (!$pcies) {
            throw $this->createNotFoundException('PCI not found');
        }

        $form = $this->createForm(PCIType::class, $pcies);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to a success page or do something else
            return $this->redirectToRoute('pcies_view');
        }

        return $this->render('admin/create_form/create_pci.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // ================Reviews================
    #[Route('/admin/reviews', name: 'reviews_view', methods: ['GET', 'HEAD'])]
    public function viewReviews(ProductsRepository $productsRepository, ReviewsRepository $reviewsRepository, Request $request)
    {

        $search = $request->query->get('search');
        $selectedProduct = $request->query->get('product');
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sortBy = 'id';
            $sortDir = 'asc';
        } else {
            list($sortBy, $sortDir) = explode(" ", $sort);
        }

        $products = $productsRepository->findAll();
        $reviews = $reviewsRepository->search($search, $selectedProduct, $sortBy, $sortDir);


        return $this->render('admin/view/reviews.html.twig', [
            'products' => $products,
            'search' => $search,
            'sort' => $sort,
            'reviews' => $reviews,
            'Sproduct' => $selectedProduct,
        ]);
    }
    #[Route('/admin/reviews/delete/{id}', name: 'delete_review')]
    public function deleteReview(ReviewsRepository $reviewsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $reviews = $reviewsRepository->find($id);

        if (!$reviews) {
            throw $this->createNotFoundException('Review not found');
        }

        // Remove the product
        $doctrine->getManager()->remove($reviews);
        $doctrine->getManager()->flush();

        // Redirect to a success page or do something else
        return $this->redirectToRoute('reviews_view');
    }
    #[Route('/admin/reviews/edit/{id}', name: 'edit_review')]
    public function editReview(Request $request, ReviewsRepository $reviewsRepository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $reviews = $reviewsRepository->find($id);

        if (!$reviews) {
            throw $this->createNotFoundException('Reviews not found');
        }

        $form = $this->createForm(ReviewsType::class, $reviews);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reviews_view');
        }

        return $this->render('admin/create_form/create_review.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // ===============Orders=============
    #[Route('/admin/orders', name: 'orders_view', methods: ['GET', 'HEAD'])]
    public function viewOrders(OrdersRepository $ordersRepository, OrderDetailsRepository $orderDetailsRepository, Request $request)
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort', 'id asc');
        list($sortBy, $sortDir) = explode(" ", $sort);

        $orderDetails = $orderDetailsRepository->search($search);

        $orderIds = [];
        foreach ($orderDetails as $orderDetail) {
            $orderIds[] = $orderDetail->getOrder()->getId();
        }

        $orders = $ordersRepository->search($orderIds, $sortBy, $sortDir);


        
        // $ordersDetails = $orderDetailsRepository->search($search);

        return $this->render('admin/view/orders.html.twig', [
            'orders' => $orders,
            'search' => $search,
            'sort' => $sort
        ]);
    }
}
