<?php

namespace PrestaSafe\PrettyBlocks\Core\Layout;

use Cache;
use Db;
use Module;
use PrettyBlocksMigrate;
use PrettyBlocksModel;
use PrestaSafe\PrettyBlocks\Core\FieldCore;

final class LayoutApplier
{
    public static function apply(array $preset, string $hookName, int $idLang, int $idShop): array
    {
        $created = [];

        if (empty($preset['blocks']) || empty($hookName)) {
            return $created;
        }

        $blocks = $preset['blocks'];
        usort($blocks, static function (array $a, array $b) {
            return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
        });

        $currentPosition = PrettyBlocksModel::getMaxPositionByZone($hookName, $idLang, $idShop);

        foreach ($blocks as $blockPreset) {
            $definition = PrettyBlocksModel::loadBlock($blockPreset['code'] ?? '');

            if (empty($definition)) {
                continue;
            }

            $model = new PrettyBlocksModel(null, $idLang, $idShop);
            $model->zone_name = $hookName;
            $model->code = $definition['code'];
            $model->name = $definition['name'] ?? $definition['code'];
            $model->instance_id = uniqid();
            $model->id_shop = $idShop;
            $model->id_lang = $idLang;
            $model->position = ++$currentPosition;

            $model->config = json_encode(self::buildConfig($definition, $blockPreset['defaults'] ?? []), true);
            $model->state = json_encode(self::buildState($definition, $blockPreset['defaults']['repeater'] ?? []), true);

            $templateName = self::resolveTemplate($definition, $blockPreset['template']['name'] ?? null);
            $model->setCurrentTemplate($templateName);
            $model->setDefaultParams($definition['default'] ?? []);

            if ($model->save()) {
                $created[] = (int) $model->id;
                self::persistLegacyLangRow($model);
            }
        }

        self::refreshHookPositions($hookName, $idLang, $idShop);
        Cache::clean('prettyblocks');
        Module::getInstanceByName('prettyblocks')->clearCache('*');

        return $created;
    }

    private static function buildConfig(array $definition, array $defaults): array
    {
        $config = [];
        $fields = $definition['config']['fields'] ?? [];

        foreach ($fields as $name => $field) {
            $value = $defaults[$name] ?? ($field['default'] ?? null);
            $config[$name] = (new FieldCore($field))->setAttribute('new_value', $value)->compile();
        }

        return $config;
    }

    private static function buildState(array $definition, array $repeaterDefaults): array
    {
        $state = [];
        $groups = $definition['repeater']['groups'] ?? [];

        foreach ($repeaterDefaults as $index => $values) {
            $row = [];
            foreach ($groups as $name => $groupDefinition) {
                $value = $values[$name] ?? ($groupDefinition['default'] ?? null);
                $row[$name] = (new FieldCore($groupDefinition))->setAttribute('new_value', $value)->compile();
            }
            // states are 1-indexed in the UI
            $state[$index + 1] = $row;
        }

        return $state;
    }

    private static function resolveTemplate(array $definition, ?string $presetTemplate): string
    {
        if ($presetTemplate && isset($definition['templates'][$presetTemplate])) {
            return $presetTemplate;
        }

        if (isset($definition['templates']['default'])) {
            return 'default';
        }

        return array_key_first($definition['templates'] ?? []) ?: 'default';
    }

    private static function persistLegacyLangRow(PrettyBlocksModel $model): void
    {
        if (!PrettyBlocksMigrate::tableExists('prettyblocks_lang')) {
            return;
        }

        $table = _DB_PREFIX_ . 'prettyblocks_lang';
        $columns = array_column(Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $table . '`'), 'Field');

        $data = [
            'id_prettyblocks' => (int) $model->id,
            'id_lang' => (int) $model->id_lang,
            'id_shop' => (int) $model->id_shop,
        ];

        $mirrorFields = [
            'state' => $model->state,
            'name' => $model->name,
            'config' => $model->config,
            'template' => $model->template,
            'default_params' => $model->default_params,
            'zone_name' => $model->zone_name,
            'position' => $model->position,
        ];

        foreach ($mirrorFields as $column => $value) {
            if (in_array($column, $columns, true)) {
                $data[$column] = $value;
            }
        }

        Db::getInstance()->insert('prettyblocks_lang', $data);
    }

    private static function refreshHookPositions(string $hookName, int $idLang, int $idShop): void
    {
        $db = Db::getInstance();
        $rows = $db->executeS(
            'SELECT id_prettyblocks FROM `' . _DB_PREFIX_ . 'prettyblocks`' .
            " WHERE zone_name = '" . pSQL($hookName) . "'" .
            ' AND id_lang = ' . (int) $idLang .
            ' AND id_shop = ' . (int) $idShop .
            ' ORDER BY position, id_prettyblocks'
        );

        $position = 0;
        foreach ($rows as $row) {
            $db->update(
                'prettyblocks',
                ['position' => ++$position],
                'id_prettyblocks = ' . (int) $row['id_prettyblocks']
            );
        }
    }
}
