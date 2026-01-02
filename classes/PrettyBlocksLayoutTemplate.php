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
class PrettyBlocksLayoutTemplate extends ObjectModel
{
    public $id_prettyblocks_layout_template;
    public $name;
    public $zone_name;
    public $id_shop;
    public $id_lang;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'prettyblocks_layout_template',
        'primary' => 'id_prettyblocks_layout_template',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true],
            'zone_name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],
        ],
    ];

    public static function findByName($name, $id_lang, $id_shop)
    {
        $collection = new PrestaShopCollection(__CLASS__, (int) $id_lang);
        $collection->where('name', '=', pSQL($name));
        $collection->where('id_shop', '=', (int) $id_shop);

        return $collection->getFirst();
    }

    public static function findById($id_template, $id_lang, $id_shop)
    {
        if (!$id_template) {
            return null;
        }

        $template = new self((int) $id_template, (int) $id_lang);

        if ((int) $template->id_shop !== (int) $id_shop) {
            return null;
        }

        return $template;
    }

    public static function getTemplates($id_lang, $id_shop)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('prettyblocks_layout_template')
            ->where('id_lang = ' . (int) $id_lang)
            ->where('id_shop = ' . (int) $id_shop)
            ->orderBy('date_add DESC');

        return Db::getInstance()->executeS($query);
    }
}
