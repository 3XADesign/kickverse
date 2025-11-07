<?php
/**
 * Language Controller
 * Handles language switching
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Customer.php';

class LangController extends Controller {
    /**
     * Change language
     */
    public function change() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['lang']) || !in_array($data['lang'], ['es', 'en'])) {
            $this->json([
                'success' => false,
                'message' => 'Invalid language'
            ], 400);
            return;
        }

        // Set language in session
        i18n::setLang($data['lang']);

        // If user is logged in, save to database
        if ($this->isLoggedIn()) {
            $user = $this->getUser();
            $customerModel = new Customer();

            try {
                $customerModel->update($user['customer_id'], [
                    'preferred_language' => $data['lang']
                ]);
            } catch (Exception $e) {
                error_log("Failed to update user language preference: " . $e->getMessage());
                // Don't fail the request - session change still works
            }
        }

        $this->json([
            'success' => true,
            'message' => 'Language changed successfully'
        ]);
    }
}
