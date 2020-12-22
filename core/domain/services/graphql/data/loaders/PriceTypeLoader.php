<?php

namespace EventEspresso\core\domain\services\graphql\data\loaders;

use EE_Error;
use EEM_Base;
use EEM_Price_Type;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use InvalidArgumentException;

/**
 * Class PriceTypeLoader
 */
class PriceTypeLoader extends AbstractLoader
{
    /**
     * @return EEM_Base
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    protected function getQuery(): EEM_Base
    {
        return EEM_Price_Type::instance();
    }

    /**
     * @param array $keys
     * @return array
     */
    protected function getWhereParams(array $keys): array
    {
        return [
            'PRT_ID' => ['IN', $keys],
        ];
    }
}
