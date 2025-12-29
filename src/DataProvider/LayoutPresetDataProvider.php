<?php

namespace PrestaSafe\PrettyBlocks\DataProvider;

use Db;
use PrettyBlocksMigrate;

final class LayoutPresetDataProvider
{
    public static function getByHook(string $hookName, int $idLang, int $idShop): ?array
    {
        if (!PrettyBlocksMigrate::tableExists('prettyblocks_layout_presets')) {
            return null;
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'prettyblocks_layout_presets` WHERE hook_name = \'' . pSQL($hookName) . '\'' .
            ' AND id_lang = ' . (int) $idLang .
            ' AND id_shop = ' . (int) $idShop;

        $row = Db::getInstance()->getRow($sql);

        return $row ?: null;
    }

    public static function getAll(int $idLang, int $idShop): array
    {
        if (!PrettyBlocksMigrate::tableExists('prettyblocks_layout_presets')) {
            return [];
        }

        $sql = 'SELECT hook_name, preset FROM `' . _DB_PREFIX_ . 'prettyblocks_layout_presets`' .
            ' WHERE id_lang = ' . (int) $idLang . ' AND id_shop = ' . (int) $idShop;

        return Db::getInstance()->executeS($sql) ?: [];
    }
}
