<?php

namespace App\Controllers;

class HomeController {    public function index() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . url('/dashboard'));
            exit;
        }

        $data = [
            'title' => 'Jobflow - La gestion simplifiée pour les freelances'
        ];

        render('home', $data);
    }
}
