<?php

namespace RouteDocs\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
abstract class httpMethod
{
    public function __construct(
        public string  $path,
        public ?string $name = null,
    ) {}

    public static function method(): string
    {
        return strtoupper(class_basename(static::class));
    }
}
