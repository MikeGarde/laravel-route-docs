<?php

namespace RouteDocs\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class queryParam extends param
{
    public function __construct(
        string        $key,
        ?string       $cast = null,
        bool          $required = false,
        ?string       $description = '',
        ?string       $example = null,
    ) {
        parent::__construct('query', $key, $cast, $required, $description, $example);
    }
}
