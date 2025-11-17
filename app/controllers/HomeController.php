<?php
namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;

class HomeController extends Controller
{
  private $artikelModel;

  public function __construct()
    {      
      $this->artikelModel = $this->model('Artikel');
    }

  public function index(): void
  {
    // ambil 3 artikel terbaru dari kedua kategori(environment_school dan event_sekolah)
    // $articles = $this->artikelModel->getByCategories(['school_environment', 'event_sekolah'], 3);

    // ambil artikel lain (kategori selain dua itu)
    // $otherArticles = $this->artikelModel->getExceptCategories(['school_environment', 'event_sekolah'], 6);

    // Ambil artikel dari kategori tertentu
    $featuredArticles = $this->artikelModel->getArticlesByCategory(
      ['school_environment', 'event_sekolah'], 3
    );

    // Ambil artikel selain kategori tersebut
    $otherArticles = $this->artikelModel->getArticlesByCategory(
      ['school_environment', 'event_sekolah'], 3,true // exclude = true
    );

    $this->view('layouts/public_main', [
      'title' => 'Beranda',
      'content' => 'home/index',
      'page' => 'home',
      'base_url' => Config::get('base_url'),
      'featuredArticles' => $featuredArticles,
      'otherArticles' => $otherArticles,
    ]);
  }
}