<?php
/**
 * Auth Page Controller
 * Handles login and register pages
 */

require_once __DIR__ . '/Controller.php';

class AuthPageController extends Controller {

    /**
     * Login page
     */
    public function login() {
        // If already logged in, redirect to account
        if ($this->isLoggedIn()) {
            $this->redirect('/mi-cuenta');
        }

        $this->view('auth/login', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * Register page
     */
    public function register() {
        // If already logged in, redirect to account
        if ($this->isLoggedIn()) {
            $this->redirect('/mi-cuenta');
        }

        $this->view('auth/register', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
