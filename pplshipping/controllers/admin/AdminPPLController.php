<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use \Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Component\HttpFoundation\Request;

abstract class AdminPPLController extends \PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController {

    private $token;

    public function __construct($token = null)
    {
        $this->token = $token;
    }


    public function getToken() {
        if ($this->token)
            return $this->token;

        $username = $this->userProvider->getUsername();
        return $this->tokenManager->getToken($username)->getValue();
    }

    private $tokenManager;

    public function setTokenManager(\Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    private $userProvider;

    public function setUserProvider(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function initShop($id_shop_group = null, $id_shop = null)
    {
        if ($id_shop_group) {
            $shopGroup = new ShopGroup($id_shop_group);
            $shops = $shopGroup->getAssociatedShops();
            Context::getContext()->shop = new Shop(reset($shops));
            Context::getContext()->shop->id = null;
        }

        else if ($id_shop)
        {
            Context::getContext()->shop = new Shop($id_shop);
        }
    }

    public function send400($errors = null)
    {

        if (!$errors) {
            return new JsonResponse(array(
                "code" => "InvalidJson",
                "errors" => [
                    "InvalidJson" => "Invalid json data"
                ]), 400);
        }
        else {
            return new JsonResponse(array(
                "data" => [
                    "code" => "element.error.dataerror.validation",
                    "errors" => $errors->errors,
                    "errors_data" => $errors->error_data
                ]
            ), 400);
        }
    }

    public function send403() {
        return new Response("", 403);
    }

    public function sendJsonModel($data)
    {
        return new JsonResponse(pplcz_normalize($data));
    }

    public function tryModelFromArray(Request $request, string $model, ?array $data = null)
    {
        if (!$data)
            $data = $this->getJson($request);
        return pplcz_denormalize($data, $model);
    }

    public function getJson(Request $request, $class = null)
    {
        $token = $request->query->get("_token");

        if ($token !== $this->getToken()) {
            return $this->send403();
        }
        $inputJSON = file_get_contents('php://input');
        $input = @json_decode($inputJSON, true); // true znamená, že chceme asociativní pole
        if (is_array($input)) {
            array_walk($input, function (&$value) {
                if (is_string($value))
                    $value = strip_tags($value);

            });
        }
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->send400();
        }
        if ($class)
            return pplcz_denormalize($input, $class);
        return $input;
    }

}