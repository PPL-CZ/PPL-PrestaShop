<?php

namespace PPLShipping\Safe;

use PPLShipping\Safe\Exceptions\RpminfoException;
/**
 * Add an additional retrieved tag in subsequent queries.
 *
 * @param int $tag One of RPMTAG_* constant, see the rpminfo constants page.
 * @throws RpminfoException
 *
 */
function rpmaddtag(int $tag) : void
{
    \error_clear_last();
    $result = \PPLShipping\rpmaddtag($tag);
    if ($result === \false) {
        throw RpminfoException::createFromPhpError();
    }
}
