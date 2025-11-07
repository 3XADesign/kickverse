<?php
/**
 * Internationalization Helper
 * Simple i18n system for multi-language support
 */

class i18n {
    private static $lang = 'es';
    private static $translations = [];

    /**
     * Initialize i18n system
     */
    public static function init($defaultLang = 'es') {
        // Detect language from session, cookie, or browser
        if (isset($_SESSION['lang'])) {
            self::$lang = $_SESSION['lang'];
        } elseif (isset($_COOKIE['lang'])) {
            self::$lang = $_COOKIE['lang'];
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            self::$lang = in_array($browserLang, ['es', 'en']) ? $browserLang : $defaultLang;
        } else {
            self::$lang = $defaultLang;
        }

        // Load translations
        self::loadTranslations(self::$lang);
    }

    /**
     * Load translation file
     */
    private static function loadTranslations($lang) {
        $file = __DIR__ . "/../lang/{$lang}.php";
        if (file_exists($file)) {
            self::$translations = require $file;
        }
    }

    /**
     * Get translation
     */
    public static function t($key, $default = null) {
        $keys = explode('.', $key);
        $value = self::$translations;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default ?? $key;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Get current language
     */
    public static function getLang() {
        return self::$lang;
    }

    /**
     * Set language
     */
    public static function setLang($lang) {
        if (in_array($lang, ['es', 'en'])) {
            self::$lang = $lang;
            $_SESSION['lang'] = $lang;
            setcookie('lang', $lang, time() + (86400 * 30), '/');
            self::loadTranslations($lang);
        }
    }
}

/**
 * Translation helper function
 */
function __($key, $default = null) {
    return i18n::t($key, $default);
}
