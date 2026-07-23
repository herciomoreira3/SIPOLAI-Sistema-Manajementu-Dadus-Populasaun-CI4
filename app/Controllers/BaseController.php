<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['form', 'url', 'menu', 'auth', 'photo'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function currentUserId(): ?int
    {
        if (! function_exists('user') || ! user()) {
            return null;
        }

        return (int) user()->id;
    }

    protected function currentUserAldeiaId(): ?int
    {
        if (! function_exists('user') || ! user() || empty(user()->id_aldeia)) {
            return null;
        }

        return (int) user()->id_aldeia;
    }

    protected function canAccessAldeia($idAldeia): bool
    {
        if (function_exists('in_groups') && in_groups('xefe-aldeia')) {
            $userAldeia = $this->currentUserAldeiaId();

            return $userAldeia !== null && (int) $idAldeia === $userAldeia;
        }

        return true;
    }

    protected function hasAnyRole(array $roles): bool
    {
        return function_exists('in_groups') && in_groups($roles);
    }

    protected function redirectForbidden(string $message = 'Ita boot la iha autorizasaun ba asaun ne\'e.')
    {
        return redirect()->back()->with('error', $message)->with('sweet-error', $message);
    }
}
