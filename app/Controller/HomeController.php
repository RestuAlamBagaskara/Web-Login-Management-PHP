<?php

    namespace Alambagaskara\LoginManagement\Controller;

    use Alambagaskara\LoginManagement\App\View;

    class HomeController {

        public function index(): void {
            View::render('Home/index', [
                'title' => 'Web Login Managemnet',
                'content' => "Selamat Belajar PHP MVC"
            ]);
        }
    }