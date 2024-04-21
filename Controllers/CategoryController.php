<?php
/*
    controllers/categories/index.php
*/
namespace app\Controllers;

use app\Core\Controller;
use app\Models\Category;
use app\Core\Request;
use app\Services\CategoryService;
use app\Middlewares\AdminMiddleware;

/**
 * Class CategoryController
 *
 * This class is responsible for handling the category operations of the application.
 * It extends the base Controller class and uses services for categories.
 * It also uses middleware for administrative tasks.
 *
 * @package app\Controllers
 */
class CategoryController extends Controller {

    /**
     * @var CategoryService $categoryService An instance of CategoryService to handle category-related operations.
     */
    private CategoryService $categoryService;

    /**
     * CategoryController constructor.
     *
     * Initializes the services and registers the middleware.
     */
    public function __construct() 
    {
        $this->categoryService = new CategoryService();
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
    }

    /**
     * Method index
     *
     * Fetches all categories and renders the 'categories' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
    {
        $categories = $this->categoryService->getAllCategories();
        $this->setLayout('admin');
        return $this->render('/admin/categories/categories', [
            'category' => $categories
        ]);      
    }

    /**
     * Method details
     *
     * Fetches the category by ID and renders the 'details_category' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function details(Request $request): array|bool|string
    {
        if($request->getMethod() === 'get') {
            $id = $request->getParam('id');
            $categoryModel = $this->categoryService->getCategoryById($id);
            $this->setLayout('admin');
            return $this->render('/admin/categories/details_category', [
                'model' => $categoryModel
            ]);
        }

        return false;
    }

    /**
     * Method create
     *
     * Handles the creation of a new category.
     * If the request method is 'post', it loads the request data into the category model,
     * saves the category, and redirects to the details page of the created category.
     * Sets the layout to 'admin' and renders the 'create_category' view with the category model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function create(Request $request): array|bool|string
    {
        $categoryModel = new Category();
        if($request->getMethod() === 'post') {
            $categoryModel->loadData($request->getBody());
            $categoryModel->save();
            return $this->redirect('/admin/categories/details_category?id=' . $categoryModel->getId());
        }

        $this->setLayout('admin');
        return $this->render('/admin/categories/create_category', [
            'model' => $categoryModel
        ]);
    }

    /**
     * Deletes a category.
     *
     * This method handles both GET and POST requests. If the request method is POST, it deletes the category
     * with the provided ID and redirects the user back to the previous page. If the request method is GET, it
     * renders the 'delete_category' view in the 'admin' layout, passing the category model to the view.
     *
     * @param Request $request The request object, which contains the HTTP request information.
     * @return mixed The response. If the request method is POST, it redirects the user back to the previous page.
     *               If the request method is GET, it renders the 'delete_category' view.
     */
    public function delete(Request $request): mixed
    {
        $id = $request->getParam('id');
        $categoryModel = $this->categoryService->getCategoryById($id);
        if($request->getMethod() === 'post') {
            $categoryModel->delete();
            return $this->back(); 
        }

        $this->setLayout('admin');
        return $this->render('/admin/categories/delete_category', [
            'model' => $categoryModel
        ]);
    }

    /**
     * Method update
     *
     * Handles the update operation for a category.
     * If the request method is 'post', it loads the request data into the category model,
     * updates the category, and redirects to the details page of the updated category.
     * If the request method is 'get', it renders the 'edit_category' view in the 'admin' layout with the category model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function update(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $categoryModel = $this->categoryService->getCategoryById($id);
        if ($request->getMethod() === 'post') {
            $categoryModel->loadData($request->getBody());
            $categoryModel->update();
            return $this->refresh();
        }

        $this->setLayout('admin');
        return $this->render('/admin/categories/edit_category', [
            'categoryModel' => $categoryModel
        ]);
    }
}
