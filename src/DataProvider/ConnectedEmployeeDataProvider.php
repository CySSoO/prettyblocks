<?php
/**
 * Copyright (c) Since 2020 PrestaSafe
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@prestasafe.com so we can send you a copy immediately.
 *
 * @author    PrestaSafe <contact@prestasafe.com>
 * @copyright Since 2020 PrestaSafe
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaSafe
 */

namespace PrestaSafe\PrettyBlocks\DataProvider;

final class ConnectedEmployeeDataProvider
{
    /**
     * Return the number of connected employees on the page
     *
     * @return int|null
     */
    public static function get(): ?int
    {
        try {
            $sql = '
                SELECT COUNT(*) 
                FROM `' . _DB_PREFIX_ . 'prettyblocks_connected_employee` 
                WHERE last_update > DATE_SUB(NOW(), INTERVAL 60 SECOND)
            ';

            return (int) \Db::getInstance()->getValue($sql);
        } catch (\Exception $e) {
            return null;
        }
    }
}
