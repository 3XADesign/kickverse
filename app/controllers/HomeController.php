<?php
/**
 * Home Controller
 * Handles the main homepage
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/League.php';

class HomeController extends Controller {
    private $productModel;
    private $leagueModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->leagueModel = new League();
    }

    /**
     * Homepage
     */
    public function index() {
        try {
            // Get featured products
            $featuredProducts = $this->productModel->getFeatured();

            // Add images to each product
            foreach ($featuredProducts as &$product) {
                $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC";
                $product['images'] = $this->productModel->fetchAll($sql, [$product['product_id']]);
            }

            // Get active leagues
            $leagues = $this->leagueModel->getAllActive();

            // Get latest products
            $latestProducts = $this->productModel->getActive(12);

            // Add images to latest products
            foreach ($latestProducts as &$product) {
                $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC";
                $product['images'] = $this->productModel->fetchAll($sql, [$product['product_id']]);
            }

            // Get best sellers (random products - will change with each page load)
            $bestSellers = $this->productModel->getRandom(3);
            foreach ($bestSellers as &$product) {
                $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC";
                $product['images'] = $this->productModel->fetchAll($sql, [$product['product_id']]);
            }

            // Get random hero products (2 products for hero banner background)
            $heroProducts = $this->productModel->getRandom(2);
            foreach ($heroProducts as &$product) {
                $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC LIMIT 1";
                $images = $this->productModel->fetchAll($sql, [$product['product_id']]);
                $product['main_image'] = !empty($images[0]['image_path']) ? $images[0]['image_path'] : '/img/placeholder.png';
            }

            $this->view('home', [
                'featured_products' => $featuredProducts,
                'leagues' => $leagues,
                'latest_products' => $latestProducts,
                'best_sellers' => $bestSellers,
                'hero_products' => $heroProducts,
                'page_title' => 'Kickverse - Camisetas de Fútbol Premium',
                'csrf_token' => $this->generateCSRF()
            ]);
        } catch (Exception $e) {
            die('Error loading homepage: ' . $e->getMessage());
        }
    }
}
