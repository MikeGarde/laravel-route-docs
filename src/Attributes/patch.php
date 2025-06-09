<?php

namespace RouteDocs\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class patch extends httpMethod {}
