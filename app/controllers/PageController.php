<?php
/**
 * Static Page Controller
 * Handles static pages like FAQ, Contact, About
 */

require_once __DIR__ . '/Controller.php';

class PageController extends Controller {

    /**
     * How it works page
     */
    public function howItWorks() {
        $this->view('pages/how-it-works', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * FAQ page
     */
    public function faq() {
        $this->view('pages/faq', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * Contact page
     */
    public function contact() {
        $this->view('pages/contact', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * About page
     */
    public function about() {
        $this->view('pages/about', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
