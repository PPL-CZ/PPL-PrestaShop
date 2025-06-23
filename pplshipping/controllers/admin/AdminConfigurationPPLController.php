<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PPLShipping\Symfony\Component\Yaml\Yaml;

class AdminConfigurationPPLController extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        if (isset($_GET['pplpath']))
        {
            $v = realpath(__DIR__ . '/../../config/routes.yml');
            $config = Yaml::parseFile(__DIR__ . '/../../config/routes.yml');
            foreach ($config as $route => $path)
            {
                $updatedPath = preg_replace_callback("~{([a-zA-Z0-9]+)}~", function($matches)  use ($path) {
                    return "(?<{$matches[1]}>[^/]+)";
                }, $path['path']);
                $match = [];

                if (preg_match("~$updatedPath$~", $_GET['pplpath'], $match))
                {
                    $request = Request::createFromGlobals();

                    list($controller, $method) = explode("::", $path['defaults']['_controller']);
                    if (!@$path['methods'] && $request->getMethod() !== 'GET' || !in_array($request->getMethod(), $path['methods'], true))
                        continue;

                    $token = @$_GET['token'];
                    $controller = new $controller($token);
                    $method = new ReflectionMethod($controller, $method);
                    $data = [];

                    $request->query->set("_token", $token);

                    foreach ($method->getParameters() as $parameter) {
                        if ($parameter->name === 'request')
                        {
                            $data[] = $request;
                        } else {
                            $data[] = $match[$parameter->name];
                        }
                    }
                    /**
                     * @var Response $response
                     */
                    $response = $method->invoke($controller, ...$data);

                    if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse)
                    {
                        $file = $response->getFile();
                        $tempdir = sys_get_temp_dir();
                        $readable = is_readable($file->getPath());
                        $fileexists = file_exists($file->getPath());
                        $size = filesize($file->getPath());
                        $content = file_get_contents($file->getPath());
                        $res = fopen($file->getPath(), "r");

                        $output = [];
                        while ($data = fread($res, 1024*100))
                        {
                            $output[] = $data;
                        }
                        $output = join('', $output);
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . strlen($output));
                        exit($output);
                    } else {
                        $response->send();
                        exit;
                    }
                }
            }
        }


        $this->bootstrap = true;

        $version = _PS_VERSION_;
        $variables = [];
        if (strpos($version, "1.7") !== 0)
        {
            $variables["pplnewpresta"] =  true;
            $this->context->smarty->assign($variables);
        }
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/configure.tpl');
        parent::initContent();
    }
}