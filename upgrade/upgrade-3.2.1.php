<?php
/**
 * Copyright (c) Since 2020 PrestaSafe and contributors
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
 * @copyright Since 2020 PrestaSafe and contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaSafe
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param prettyblocks $module
 *
 * @return bool|string
 */
function upgrade_module_3_2_1($module)
{
    if (!PrettyBlocksMigrate::tableExists('prettyblocks_layout_template')) {
        $db = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prettyblocks_layout_template` (
            `id_prettyblocks_layout_template` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `zone_name` varchar(255) DEFAULT NULL,
            `id_shop` int(11) DEFAULT NULL,
            `id_lang` int(11) DEFAULT NULL,
            `date_add` datetime DEFAULT NULL,
            `date_upd` datetime DEFAULT NULL,
            PRIMARY KEY (`id_prettyblocks_layout_template`)
          ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        Db::getInstance()->execute($db);
    }

    return true;
}
