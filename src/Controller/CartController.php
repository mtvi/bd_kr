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
        $totalPrice = 0;
        foreach ($cartProducts as $product) {
            $productId = $product->getId();
            $totalPrice += $product->getPrice()*$productQuantities[$productId];
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
            'totalPrice' => $totalPrice
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(SessionInterface $sessionInterface, $id, ProductsRepository $productsRepository, Request $request): Response
    {
        $cartProductIds = $sessionInterface->get('cart', []);
        $productQuantities = array_count_values($cartProductIds); // Count product quantities
        
        
        $product = $productsRepository->findOneBy(['id' => $id]);
        if(!in_array($id, $cartProductIds) || $product->getQuantity()>$productQuantities[$id])
            $cartProductIds[] = $id;

                

        // Update the session cart data
        $sessionInterface->set('cart', $cartProductIds);
        $referer = $request->headers->get('referer');
        // Redirect back to the product list or cart page
        return $this->redirect($referer);
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart(SessionInterface $sessionInterface, $id, Request $request): Response
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

        $referer = $request->headers->get('referer');
        // Redirect back to the product list or cart page
        return $this->redirect($referer);
    }

    #[Route('/clear-cart', name: 'clear_cart')]
    public function clearCart(SessionInterface $sessionInterface, Request $request): Response
    {
        // Clear the entire cart
        $sessionInterface->remove('cart');

        $referer = $request->headers->get('referer'); 
        // Redirect back to the product list or cart page
        return $this->redirect($referer);
    }

    #[Route('/order/create', name: 'cart_continue')]
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
            $orderedQuantity = $productQuantities[$product->getId()];
            $orderDetail->setQuantity($orderedQuantity);
            $orderDetail->setProduct($product);
            $orderDetail->setOrder($order);
            //$order->addOrderDetail($orderDetail);
            $entityManager->persist($orderDetail);
            $product->Ordered($orderedQuantity);

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
            
            $entityManager->persist($order);
            $entityManager->flush();


            // Clear the cart data from the session
            $sessionInterface->set('cart', []);

            return $this->redirectToRoute('products_list');
        }

        return $this->render('cart/create_order.html.twig', [
            'totalPrice' => $totalPrice,
            'form' => $form->createView(),
            'cartData' => $cartProducts,
            'productQuantities' => $productQuantities,
        ]);
    }

}
