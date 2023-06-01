<?php
/**
 * SwivelDataSource.php
 *
 * @created 7/28/21
 * @version 1.0
 * @author Dana Luther <dana.luther@gmail.com>
 * @yiiVersion 2.0.41
 */

namespace dhluther\swivel;

/**
 * SwivelDataSource
 *
 * Adds support for strong typing without tying the codebase to the SwivelFeature model
 */
interface SwivelDataSource
{
    /**
     * Return an array of map data in the format that Swivel expects
     *
     * @return array
     */
    public function getMapData(): array;
}
