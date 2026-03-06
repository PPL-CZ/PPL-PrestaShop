<?php


use PPLShipping\Model\Model\SendErrorLogModel;
use PPLShipping\Model\Model\ErrorLogModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminLogPPLController extends  AdminPPLController
{
    public function GetLogs(Request $request)
    {
        if (!$this->isTokenValid($request->get("_token")))
            return $this->send403();

        $product_ids = [];
        $order_ids = [];

        if ($request->query->get("product_ids"))
            $product_ids = array_map('intval', explode(',', $request->query->get('product_ids')));
        if ($request->query->get('order_ids'))
            $order_ids = array_map('intval', explode(',', $request->query->get('order_ids')));

        $logModel = pplcz_denormalize(new ErrorLogModel(), ErrorLogModel::class, [
            'product_ids' => $product_ids,
            'order_ids' => $order_ids
        ]);
        $logModel = pplcz_normalize($logModel);

        return new JsonResponse($logModel);
    }

    public function DeleteLog(Request $request)
    {
        if (!$this->isTokenValid($request->get("_token")))
            return $this->send403();

        $data = $this->getJson($request);
        $id = $data['id'] ?? null;

        if (!$id) {
            return new Response("Missing id", 400);
        }

        $log = new \PPLLog($id);

        if (!$log->id) {
            return new Response("", 404);
        }

        $log->delete();

        return new Response("", 204);
    }

    public function SendLogs(Request $request)
    {
        if (!$this->isTokenValid($request->get("_token")))
            return $this->send403();

        /**
         * @var SendErrorLogModel $inputError
         */
        $inputError = $this->tryModelFromArray($request, SendErrorLogModel::class);
        $errors = new \PPLShipping\Errors();

        pplcz_validate($inputError, "", $errors);

        if ($errors->errors)
        {
            return $this->send400($errors);
        }


        $message = $inputError->getMessage();
        $mail = $inputError->getMail();
        $message  = "Kontakt: " . $mail . "\n\n" . $message;
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $mail = null;
        }

        $info = $inputError->getInfo();
        $errors = trim(join("\n\n", array_map(function ($msg) {
            return $msg->getTrace();
        },$inputError->getErrors())));

        $textMessage = $message . "\n" . $info . "\nVýpis systémových logů\n" . $errors;
        $htmlMessage ="<p>" . str_replace("\n", "<br>",htmlspecialchars($message)) . "</p><p>" . str_replace("\n", "<br>", htmlspecialchars($info));

        $adminEmail = Configuration::get('PS_SHOP_EMAIL');
        $fileAttachment = [
            'content' => $errors,
            'name' => "Logy.txt",
            "mine"=>"text/plain"
        ];

        if (!$errors)
            $fileAttachment = null;


        \Mail::send(
            (int)Configuration::get('PS_LANG_DEFAULT'),
            'pplreport',
            'PrestaShop Plugin - nahlášení problému',
            [
                '{htmlMessage}' => $htmlMessage,
                '{textMessage}' => $textMessage
            ],
            "cisteam@ppl.cz",
            null,
            $adminEmail ?: null,
            null,
            $fileAttachment,
            null,
            __DIR__ . '/../../',
            false,
            (int)Context::getContext()->shop->id,
            null
        );

        $response = new Response("", 204);

        return $response;
    }

}