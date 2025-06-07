<?php

namespace Support\Middleware;

use Closure;
use Domain\Users\Models\User;
use Hamcrest\Core\Set;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Support\Actions\SetPaginationAction;
use Symfony\Component\HttpFoundation\Response;

class PerPage
{
    private                     $user;
    private                     $perPage;
    private                     $model;
    private SetPaginationAction $setPaginationAction;

    public function __construct()
    {
        $this->setPaginationAction = new SetPaginationAction();
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->requestChangesPage()) {
            ($this->setPaginationAction)($this->getModel(), $this->getUser(), $this->getPerPage());
        }

        return $next($request);
    }

    public function requestChangesPage()
    {
        return $this->getPerPage() && $this->getUser() && $this->getModel();
    }

    public function getPerPage(): ?string
    {
        if (!$this->perPage) {
            $this->perPage = request()->get('per_page');
        }

        return $this->perPage;
    }

    public function getUser(): Authenticatable
    {
        if (!$this->user) {
            $this->user = auth()->user();
        }

        return $this->user;
    }

    public function getModel(): ?string
    {
        if (!$this->model) {
            $controller = Route::getCurrentRoute()
                ->getController();

            if (method_exists($controller, 'getModel')) {
                $this->model = $controller->getModel();
            }
        }

        return $this->model;
    }
}
