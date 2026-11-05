<?php
/**
 * SkinCheck - Home Controller
 * Manages the client landing page logic.
 */

class HomeController extends Controller {
    /**
     * Landing Page action.
     */
    public function index() {
        $data = [
            'title' => 'SkinCheck - AI Skin Health & Sunscreen Analyzer',
            'description' => 'Analyze your facial skin health metrics, verify sunscreen application efficiency in real-time, and get AI-powered customized skin recommendations from your browser.'
        ];
        
        $this->view('home/index', $data);
    }
}
