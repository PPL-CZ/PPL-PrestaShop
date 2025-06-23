<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator;

use PPLShipping\Jane\Component\OpenApi3\Generator\Client\ServerPluginGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\ClientGenerator as BaseClientGenerator;
class ClientGenerator extends BaseClientGenerator
{
    use ServerPluginGenerator;
}
