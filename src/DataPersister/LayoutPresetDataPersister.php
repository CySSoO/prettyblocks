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

        $sql = sprintf(
            "INSERT INTO `%sprettyblocks_layout_presets` (id_lang, id_shop, hook_name, preset, date_add, date_upd) " .
            "VALUES (%d, %d, '%s', '%s', '%s', '%s') " .
            "ON DUPLICATE KEY UPDATE preset = VALUES(preset), date_upd = VALUES(date_upd)",
            _DB_PREFIX_,
            (int) $idLang,
            (int) $idShop,
            $hook,
            $value,
            $now,
            $now
        );

        return \Db::getInstance()->execute($sql);
    }

    /**
     * Remove a preset for the given context.
     */
    public static function delete(int $idLang, int $idShop, string $hookName): bool
    {
        self::ensureTable();

        $where = sprintf(
            "id_lang = %d AND id_shop = %d AND hook_name = '%s'",
            (int) $idLang,
            (int) $idShop,
            pSQL($hookName)
        );

        return \Db::getInstance()->delete('prettyblocks_layout_presets', $where);
    }

    private static function ensureTable(): void
    {
        if (!PrettyBlocksMigrate::tableExists('prettyblocks_layout_presets')) {
            Module::getInstanceByName('prettyblocks')->makeLayoutPresetTable();
        }
    }
}
