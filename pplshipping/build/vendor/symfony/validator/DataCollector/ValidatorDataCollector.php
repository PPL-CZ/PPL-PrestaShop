<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\DataCollector;

use PPLShipping\Symfony\Component\Form\FormInterface;
use PPLShipping\Symfony\Component\HttpFoundation\Request;
use PPLShipping\Symfony\Component\HttpFoundation\Response;
use PPLShipping\Symfony\Component\HttpKernel\DataCollector\DataCollector;
use PPLShipping\Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use PPLShipping\Symfony\Component\Validator\Validator\TraceableValidator;
use PPLShipping\Symfony\Component\VarDumper\Caster\Caster;
use PPLShipping\Symfony\Component\VarDumper\Caster\ClassStub;
use PPLShipping\Symfony\Component\VarDumper\Cloner\Data;
use PPLShipping\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
class ValidatorDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private $validator;
    public function __construct(TraceableValidator $validator)
    {
        $this->validator = $validator;
        $this->reset();
    }
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, ?\Throwable $exception = null)
    {
        // Everything is collected once, on kernel terminate.
    }
    public function reset()
    {
        $this->data = ['calls' => $this->cloneVar([]), 'violations_count' => 0];
    }
    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $collected = $this->validator->getCollectedData();
        $this->data['calls'] = $this->cloneVar($collected);
        $this->data['violations_count'] = \array_reduce($collected, function ($previous, $item) {
            return $previous + \count($item['violations']);
        }, 0);
    }
    public function getCalls() : Data
    {
        return $this->data['calls'];
    }
    public function getViolationsCount() : int
    {
        return $this->data['violations_count'];
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'validator';
    }
    protected function getCasters() : array
    {
        return parent::getCasters() + [\Exception::class => function (\Exception $e, array $a, Stub $s) {
            foreach (["\x00Exception\x00previous", "\x00Exception\x00trace"] as $k) {
                if (isset($a[$k])) {
                    unset($a[$k]);
                    ++$s->cut;
                }
            }
            return $a;
        }, FormInterface::class => function (FormInterface $f, array $a) {
            return [Caster::PREFIX_VIRTUAL . 'name' => $f->getName(), Caster::PREFIX_VIRTUAL . 'type_class' => new ClassStub(\get_class($f->getConfig()->getType()->getInnerType())), Caster::PREFIX_VIRTUAL . 'data' => $f->getData()];
        }];
    }
}
