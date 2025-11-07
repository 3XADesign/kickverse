<?php
/**
 * Mystery Box Page Controller
 */

require_once __DIR__ . '/Controller.php';

class MysteryBoxController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Mystery Box page (includes subscriptions/mystery boxes)
     */
    public function index() {
        $this->view('mystery-box/index', [
            'page_title' => 'Mystery Box - ' . __('mystery_box.title'),
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
