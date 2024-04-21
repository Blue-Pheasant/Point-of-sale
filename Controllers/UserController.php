<?php
/*
    controllers/user.php
*/
namespace app\Controllers;

use app\Core\Controller;
use app\Core\Request;
use app\Middlewares\AdminMiddleware;
use app\Middlewares\AuthMiddleware;
use app\Models\User;
use app\Services\UserService;
use app\Auth\AuthUser;

/**
 * Class UserController
 *
 * This class is responsible for handling the user operations of the application.
 * It extends the base Controller class and uses the User model and UserService.
 * It also uses middleware for authentication and administrative tasks.
 *
 * @package app\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserService $userService An instance of UserService to handle user-related operations.
     */
    private UserService $userService;
    
    /**
     * UserController constructor.
     *
     * Registers the middleware and initializes the UserService.
     */
    public function __construct() 
    {
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
        $this->registerMiddleware(AuthMiddleware::class, ['profile', 'updateProfile', 'password']);
        $this->userService = new UserService();
    }

    /**
     * Method index
     *
     * Fetches all users and renders the 'users' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
    {
        $users = User::getAllUsers();
        $this->setLayout('admin');
        return $this->render('/admin/users/users', [
            'users' => $users
        ]);
    }

    /**
     * Method create
     *
     * Creates a new user and saves it to the database.
     * If the user is a client, it is also saved as an admin.
     *
     * @param Request $request
     * @return array|bool|string
     */
    public function create(Request $request): array|bool|string
    {
        $userModel = new User();
        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            if($userModel->getRole() === 'client') {
                $userModel->saveAdmin($userModel->getRole());
            } else {
                $userModel->save();
            }
            return $this->refresh();
        }

        $this->setLayout('admin');
        return $this->render('/admin/users/create_user',  [
            'userModel' => $userModel
        ]);
    }

    /**
     * Method delete
     *
     * Deletes a user from the database.
     * If the request method is 'post', it deletes the user and redirects to the previous page.
     * If the request method is 'get', it renders the 'delete_user' view.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function delete(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);

        if($request->getMethod() === 'post') {
            $userModel->delete();
            return $this->back();
        }
        
        $this->setLayout('admin');
        return $this->render('/admin/users/delete_user', [
            'userModel' => $userModel
        ]);
    }

    /**
     * Method update
     *
     * Updates a user in the database.
     * If the request method is 'post', it loads the data, validates it, and updates it.
     * If the request method is 'get', it renders the 'edit_user' view.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function update(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            $userModel->update();
            return $this->refresh();
        }
        
        $this->setLayout('admin');
        return $this->render('/admin/users/edit_user', [
            'userModel' => $userModel
        ]);
    }

    /**
     * Method details
     *
     * Fetches the user by ID and renders the 'details_user' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function details(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        
        $this->setLayout('admin');
        return $this->render('/admin/users/details_user', [
            'userModel' => $userModel
        ]);         
    }

    /**
     * Method password
     *
     * Handles the password change operation for a user.
     * If the request method is 'post', it loads the request data into the user model,
     * validates the data, and if valid, updates the password.
     * If the request method is 'get', it renders the 'change_password' view in the 'admin' layout with the user model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function password(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);

        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            $userModel->update();
            return $this->refresh();
        }
        
        $this->setLayout('admin');
        return $this->render('/admin/users/change_password', [
            'userModel' => $userModel
        ]);
    }

    /**
     * Method profile
     *
     * Renders the 'profile' view with the user model.
     *
     * @return array|bool|string
     */
    public function profile(): array|bool|string
    {
        return $this->render('profile');
    }

    /**
     * Method updateProfile
     *
     * Updates the user profile.
     * If the request method is 'post', it loads the request data into the user model,
     * validates the data, and if valid, updates the profile.
     * If the request method is 'get', it renders the 'profile' view with the user model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function updateProfile(Request $request): array|bool|string
    {
        $updateSuccess = false;
        $id = AuthUser::authUser()->id;
        $user = $this->userService->getUserById($id);

        if ($request->getMethod() === 'post') {
            $user->loadData($request->getBody());
            if ($user->validateUpdateProfile()) {
                if ($user->updateProfile($user)) {
                    $updateSuccess = true;
                }
            }
        }

        return $this->render('profile', [
            'user' => $user,
            'updateSuccess' => $updateSuccess,
        ]);
    }
}