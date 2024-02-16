<?php

// src/Controller/CartController.php

namespace App\Controller;

use App\Form\OrderFormType;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Orders;
use App\Entity\OrderDetails;
use Doctrine\Persistence\ManagerRegistry;


class CartController extends AbstractController
{
    #[Route('/cart', name: 'view_cart')]
    public function viewCart(SessionInterface $sessionInterface, ProductsRepository $productsRepository): Response
    {
        $cartProductIds = $sessionInterface->get('cart', []);

        // Retrieve the product entities associated with the product IDs
        $cartProducts = $productsRepository->findBy(['id' => $cartProductIds]);

        $cartData = [];
        $productQuantities = array_count_values($cartProductIds); // Count product quantities
        
        foreach ($cartProducts as $product) {
            $productId = $product->getId();
            $cartData[] = [
                'id' => $productId,
                'image' => $product->getImage(),
                'vendor' => $product->getVendor(),
                'GPU' => $product->getGPU(),
                'price' => $product->getPrice(),
                'quantity' => $productQuantities[$productId], // Get the quantity from the counts
            ];
        }

        return $this->render('cart/view.html.twig', [
            'cartData' => $cartData, // Pass the array of product entities to the template
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(SessionInterface $sessionInterface, $id): Response
    {
        $cartProductIds = $sessionInterface->get('cart', []);

        // Check if the product ID already exists in the cart
        if (in_array($id, $cartProductIds)) {
            // Increment the quantity if it exists
            $cartProductIds[] = $id;
        } else {
            // Add the product ID to the cart if it doesn't exist
            $cartProductIds[] = $id;
        }

        // Update the session cart data
        $sessionInterface->set('cart', $cartProductIds);

        // Redirect back to the product list or cart page
        return $this->redirectToRoute('products_list');
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart(SessionInterface $sessionInterface, $id): Response
    {
        $cartProductIds = $sessionInterface->get('cart', []);

        // Check if the product ID exists in the cart
        $key = array_search($id, $cartProductIds);
        if ($key !== false) {
            // Remove the product from the cart
            unset($cartProductIds[$key]);

            // Re-index the array to remove gaps
            $cartProductIds = array_values($cartProductIds);

            // Update the session cart data
            $sessionInterface->set('cart', $cartProductIds);
        }

        // Redirect back to the cart page
        return $this->redirectToRoute('view_cart');
    }

    #[Route('/clear-cart', name: 'clear_cart')]
    public function clearCart(SessionInterface $sessionInterface): Response
    {
        // Clear the entire cart
        $sessionInterface->remove('cart');

        // Redirect back to the cart page
        return $this->redirectToRoute('view_cart');
    }

    #[Route('/order/create', name: 'create_order')]
    public function createOrder(Request $request, ManagerRegistry $doctrine, ProductsRepository $productsRepository, SessionInterface $sessionInterface): Response
    {   
        $entityManager = $doctrine->getManager();
        // Fetch cart data from the session
        $cartProductIds = $sessionInterface->get('cart', []);

        // Retrieve the product entities associated with the product IDs
        $cartProducts = $productsRepository->findBy(['id' => $cartProductIds]);

        $productQuantities = array_count_values($cartProductIds);

        // Create a new order and order details
        $totalPrice = 0;
        $order = new Orders();
        foreach ($cartProducts as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->setQuantity($productQuantities[$product->getId()]);
            $orderDetail->setProduct($product);
            $orderDetail->setOrder($order);
            //$order->addOrderDetail($orderDetail);
            $entityManager->persist($orderDetail);


            $totalPrice += $orderDetail->getQuantity()*$product->getPrice();
            
        }

        // Create the order form
        $form = $this->createForm(OrderFormType::class, $order);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setTotalPrice($totalPrice);
            $currentDateTime = new \DateTime();
            $order->setOrderDate($currentDateTime);
            // Save the order and order details
            
            $entityManager->persist($order);
            $entityManager->flush();


            // Clear the cart data from the session
            $sessionInterface->set('cart', []);

            return $this->redirectToRoute('products_list');
        }

        return $this->render('cart/create_order.html.twig', [
            'form' => $form->createView(),
            'cartData' => $cartProducts,
            'productQuantities' => $productQuantities,
        ]);
    }

}
