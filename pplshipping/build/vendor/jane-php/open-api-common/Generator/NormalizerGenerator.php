<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\NormalizerGenerator as BaseNormalizerGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Normalizer\DenormalizerGenerator as DenormalizerGeneratorTrait;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Normalizer\NormalizerGenerator as NormalizerGeneratorTrait;
class NormalizerGenerator extends BaseNormalizerGenerator
{
    use DenormalizerGeneratorTrait;
    use NormalizerGeneratorTrait;
}
