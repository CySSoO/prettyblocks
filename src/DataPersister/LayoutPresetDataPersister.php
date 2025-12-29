<?php

namespace PrestaSafe\PrettyBlocks\DataPersister;

use Module;
use PrettyBlocksMigrate;

final class LayoutPresetDataPersister
{
    /**
     * Persist layout preset selection for a given context.
     */
    public static function save(int $idLang, int $idShop, string $hookName, string $preset): bool
    {
        self::ensureTable();

        $hook = pSQL($hookName);
        $value = pSQL($preset);
        $now = date('Y-m-d H:i:s');

        $sql = '
            INSERT INTO `' . _DB_PREFIX_ . 'prettyblocks_layout_presets`
            (id_lang, id_shop, hook_name, preset, date_add, date_upd)
            VALUES (' .
            (int) $idLang . ', ' .
            (int) $idShop . ", '" . $hook . "', '" . $value . "', '" . $now . "', '" . $now . "')
            ON DUPLICATE KEY UPDATE preset = VALUES(preset), date_upd = VALUES(date_upd)
        ';

        return \Db::getInstance()->execute($sql);
    }

    /**
     * Remove a preset for the given context.
     */
    public static function delete(int $idLang, int $idShop, string $hookName): bool
    {
        self::ensureTable();

        return \Db::getInstance()->delete(
            'prettyblocks_layout_presets',
            'id_lang = ' . (int) $idLang .
            ' AND id_shop = ' . (int) $idShop .
            " AND hook_name = '" . pSQL($hookName) . "'"
        );
    }

    private static function ensureTable(): void
    {
        if (!PrettyBlocksMigrate::tableExists('prettyblocks_layout_presets')) {
            Module::getInstanceByName('prettyblocks')->makeLayoutPresetTable();
        }
    }
}
