<?php
namespace App\Http\Controllers;
use App\Actions\Category\Featured as GetFeaturedAction;
use Illuminate\View\View;

class LandingController extends Controller
{
  /**
   * Display the landing page with featured categories.
   *
   * @return View
   */
  public function index(): View
  {
    $categories = (new GetFeaturedAction())->execute();
    return view('pages.landing', [
      'categories' => $categories,
    ]);
  }
}
