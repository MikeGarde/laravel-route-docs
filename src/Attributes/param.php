<?php

namespace RouteDocs\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class param
{
    public function __construct(
        public string|object $type,
        public string        $key,
        public ?string       $cast = null,
        public bool          $required = false,
        public ?string       $description = '',
        public ?string       $example = null,
    ) {
    }
}
