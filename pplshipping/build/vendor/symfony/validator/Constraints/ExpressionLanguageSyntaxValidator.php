<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Constraints;

use PPLShipping\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use PPLShipping\Symfony\Component\ExpressionLanguage\SyntaxError;
use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\ConstraintValidator;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedValueException;
/**
 * @author Andrey Sevastianov <mrpkmail@gmail.com>
 */
class ExpressionLanguageSyntaxValidator extends ConstraintValidator
{
    private $expressionLanguage;
    public function __construct(?ExpressionLanguage $expressionLanguage = null)
    {
        $this->expressionLanguage = $expressionLanguage;
    }
    /**
     * {@inheritdoc}
     */
    public function validate($expression, Constraint $constraint) : void
    {
        if (!$constraint instanceof ExpressionLanguageSyntax) {
            throw new UnexpectedTypeException($constraint, ExpressionLanguageSyntax::class);
        }
        if (!\is_string($expression)) {
            throw new UnexpectedValueException($expression, 'string');
        }
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
        }
        try {
            $this->expressionLanguage->lint($expression, $constraint->allowedVariables);
        } catch (SyntaxError $exception) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ syntax_error }}', $this->formatValue($exception->getMessage()))->setInvalidValue((string) $expression)->setCode(ExpressionLanguageSyntax::EXPRESSION_LANGUAGE_SYNTAX_ERROR)->addViolation();
        }
    }
}
