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

namespace PrestaSafe\PrettyBlocks\Controller;

use Cache;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaSafe\PrettyBlocks\Core\Layout\LayoutApplier;
use PrestaSafe\PrettyBlocks\Core\Layout\LayoutCatalog;
use PrestaSafe\PrettyBlocks\DataPersister\LayoutPresetDataPersister;
use PrestaSafe\PrettyBlocks\DataProvider\LayoutPresetDataProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminLayoutPresetController extends FrameworkBundleAdminController
{
    public function getLayoutPresetsAction(Request $request)
    {
        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $translator = $context->getTranslator();
        $idLang = (int) $request->get('id_lang', $context->language->id);
        $idShop = (int) $request->get('id_shop', $context->shop->id);
        $hookName = (string) $request->get('hook', '');

        $selected = $hookName !== '' ? LayoutPresetDataProvider::getByHook($hookName, $idLang, $idShop) : null;
        $presets = LayoutPresetDataProvider::getAll($idLang, $idShop);
        $catalog = LayoutCatalog::getAll();
        $hooks = $this->getAvailableHooksList();
        $contextPayload = [
            'id_lang' => $idLang,
            'id_shop' => $idShop,
            'hook' => $hookName,
        ];

        if (!$request->isXmlHttpRequest() && $request->getRequestFormat() !== 'json') {
            $catalogByKey = [];
            foreach ($catalog as $preset) {
                if (!isset($preset['key'])) {
                    continue;
                }
                $catalogByKey[$preset['key']] = [
                    'label' => $preset['label'] ?? $preset['key'],
                    'description' => $preset['description'] ?? '',
                    'hooks' => $preset['hooks'] ?? [],
                    'blocks_count' => isset($preset['blocks']) ? count($preset['blocks']) : 0,
                ];
            }

            return $this->render('@Modules/prettyblocks/views/templates/admin/layout_presets.html.twig', [
                'presets' => $presets,
                'hooks' => $hooks,
                'catalog' => $catalog,
                'catalog_by_key' => $catalogByKey,
                'context_data' => $contextPayload,
                'selected' => $selected,
                'save_url' => $this->generateUrl('prettyblocks_layout_presets_save'),
                'delete_url' => $this->generateUrl('prettyblocks_layout_presets_delete'),
                'page_title' => $translator->trans('Layout presets', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        return (new JsonResponse())->setData([
            'layouts' => $this->getLayoutsFromRegisteredBlocks(),
            'catalog' => $catalog,
            'presets' => $presets,
            'selected' => $selected,
            'hooks' => $hooks,
            'context' => $contextPayload,
        ]);
    }

    public function saveLayoutPresetAction(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $payload = is_array($payload) ? $payload : [];

        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $idLang = (int) ($payload['id_lang'] ?? $request->get('id_lang', $context->language->id));
        $idShop = (int) ($payload['id_shop'] ?? $request->get('id_shop', $context->shop->id));
        $hookName = (string) ($payload['hook'] ?? $request->get('hook', ''));
        $preset = (string) ($payload['preset'] ?? $request->get('preset', ''));

        if ($hookName === '' || $preset === '') {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Missing hook or preset to save.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        $presetDefinition = LayoutCatalog::findByKey($preset);

        if ($presetDefinition === null) {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Unknown layout preset.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        $result = LayoutPresetDataPersister::save($idLang, $idShop, $hookName, $preset);

        if ($result) {
            \PrettyBlocksModel::deleteBlocksFromZone($hookName, $idLang, $idShop);
            LayoutApplier::apply($presetDefinition, $hookName, $idLang, $idShop);
            LayoutApplier::refreshHookPositions($hookName, $idLang, $idShop);
            Cache::clean('prettyblocks');
            \Module::getInstanceByName('prettyblocks')->clearCache('*');
        }

        if (!$request->isXmlHttpRequest() && $request->getRequestFormat() !== 'json') {
            $this->addFlash(
                $result ? 'success' : 'error',
                $context->getTranslator()->trans(
                    $result ? 'Layout preset saved successfully.' : 'Unable to save layout preset.',
                    [],
                    'Modules.Prettyblocks.Admin'
                )
            );

            return $this->redirectToRoute('prettyblocks_layout_presets', [
                'id_lang' => $idLang,
                'id_shop' => $idShop,
            ]);
        }

        return (new JsonResponse())->setData([
            'success' => (bool) $result,
            'preset' => [
                'hook' => $hookName,
                'preset' => $preset,
                'id_lang' => $idLang,
                'id_shop' => $idShop,
            ],
        ]);
    }

    public function deleteLayoutPresetAction(Request $request)
    {
        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $idLang = (int) $request->get('id_lang', $context->language->id);
        $idShop = (int) $request->get('id_shop', $context->shop->id);
        $hookName = (string) $request->get('hook', '');

        if ($hookName === '') {
            $message = $context->getTranslator()->trans('Missing hook to delete.', [], 'Modules.Prettyblocks.Admin');
            if (!$request->isXmlHttpRequest() && $request->getRequestFormat() !== 'json') {
                $this->addFlash('error', $message);

                return $this->redirectToRoute('prettyblocks_layout_presets', [
                    'id_lang' => $idLang,
                    'id_shop' => $idShop,
                ]);
            }

            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $message,
            ]);
        }

        $result = LayoutPresetDataPersister::delete($idLang, $idShop, $hookName);

        if ($result) {
            \PrettyBlocksModel::deleteBlocksFromZone($hookName, $idLang, $idShop);
            LayoutApplier::refreshHookPositions($hookName, $idLang, $idShop);
            Cache::clean('prettyblocks');
            \Module::getInstanceByName('prettyblocks')->clearCache('*');
        }

        if (!$request->isXmlHttpRequest() && $request->getRequestFormat() !== 'json') {
            $this->addFlash(
                $result ? 'success' : 'error',
                $context->getTranslator()->trans(
                    $result ? 'Layout preset deleted.' : 'Unable to delete layout preset.',
                    [],
                    'Modules.Prettyblocks.Admin'
                )
            );

            return $this->redirectToRoute('prettyblocks_layout_presets', [
                'id_lang' => $idLang,
                'id_shop' => $idShop,
            ]);
        }

        return (new JsonResponse())->setData([
            'success' => (bool) $result,
            'deleted' => $result,
            'context' => [
                'hook' => $hookName,
                'id_lang' => $idLang,
                'id_shop' => $idShop,
            ],
        ]);
    }

    public function previewLayoutPresetAction(Request $request)
    {
        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $payload = json_decode($request->getContent(), true);
        $payload = is_array($payload) ? $payload : [];

        $idLang = (int) ($payload['id_lang'] ?? $request->get('id_lang', $context->language->id));
        $idShop = (int) ($payload['id_shop'] ?? $request->get('id_shop', $context->shop->id));
        $hookName = (string) ($payload['hook'] ?? $request->get('hook', ''));
        $presetKey = (string) ($payload['preset'] ?? $request->get('preset', ''));

        if ($hookName === '' || $presetKey === '') {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Missing hook or preset to preview.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        $preset = LayoutCatalog::findByKey($presetKey);

        if ($preset === null) {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Unknown layout preset.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        \PrettyBlocksModel::deleteTemporaryByZone($hookName, $idLang, $idShop);
        $created = LayoutApplier::apply($preset, $hookName, $idLang, $idShop, true);

        $blocks = \PrettyBlocksModel::getInstanceByZone($hookName, 'front', $idLang, $idShop, true);
        $context->smarty->assign([
            'zone_name' => $hookName,
            'blocks' => $blocks,
            'alias' => '',
            'priority' => false,
        ]);

        $html = $context->smarty->fetch('module:prettyblocks/views/templates/front/zone.tpl');

        return (new JsonResponse())->setData([
            'success' => true,
            'created' => $created,
            'html' => $html,
            'temporary' => true,
        ]);
    }

    public function confirmLayoutPreviewAction(Request $request)
    {
        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $payload = json_decode($request->getContent(), true);
        $payload = is_array($payload) ? $payload : [];

        $idLang = (int) ($payload['id_lang'] ?? $request->get('id_lang', $context->language->id));
        $idShop = (int) ($payload['id_shop'] ?? $request->get('id_shop', $context->shop->id));
        $hookName = (string) ($payload['hook'] ?? $request->get('hook', ''));

        if ($hookName === '') {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Missing hook to confirm preview.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        $updated = \PrettyBlocksModel::promoteTemporaryByZone($hookName, $idLang, $idShop);
        LayoutApplier::refreshHookPositions($hookName, $idLang, $idShop, true);
        Cache::clean('prettyblocks');
        \Module::getInstanceByName('prettyblocks')->clearCache('*');

        return (new JsonResponse())->setData([
            'success' => (bool) $updated,
            'updated' => $updated,
        ]);
    }

    public function cancelLayoutPreviewAction(Request $request)
    {
        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $payload = json_decode($request->getContent(), true);
        $payload = is_array($payload) ? $payload : [];

        $idLang = (int) ($payload['id_lang'] ?? $request->get('id_lang', $context->language->id));
        $idShop = (int) ($payload['id_shop'] ?? $request->get('id_shop', $context->shop->id));
        $hookName = (string) ($payload['hook'] ?? $request->get('hook', ''));

        if ($hookName === '') {
            return (new JsonResponse())->setData([
                'success' => false,
                'message' => $context->getTranslator()->trans('Missing hook to cancel preview.', [], 'Modules.Prettyblocks.Admin'),
            ]);
        }

        $deleted = \PrettyBlocksModel::deleteTemporaryByZone($hookName, $idLang, $idShop);
        Cache::clean('prettyblocks');
        \Module::getInstanceByName('prettyblocks')->clearCache('*');

        return (new JsonResponse())->setData([
            'success' => (bool) $deleted,
            'deleted' => $deleted,
        ]);
    }

    private function getAvailableHooksList()
    {
        $hooks = \Hook::getHooks(true, false);

        $frontDisplayHooks = array_filter($hooks, function ($hook) {
            return preg_match('/^display(?!Admin|BackOffice)/', $hook['name']);
        });

        return array_map(function ($hook) {
            return [
                'id_hook' => (int) $hook['id_hook'],
                'name' => $hook['name'],
                'title' => $hook['title'] ?? $hook['name'],
                'description' => $hook['description'] ?? '',
            ];
        }, $frontDisplayHooks);
    }

    private function getLayoutsFromRegisteredBlocks(): array
    {
        $blocks = \PrettyBlocksModel::getBlocksAvailable();
        $layouts = [];

        foreach ($blocks as $block) {
            $templates = $block['templates'] ?? ['default' => $block['template'] ?? ''];

            foreach ($templates as $templateName => $templatePath) {
                $identifier = $block['code'] . ':' . $templateName;
                $layouts[] = [
                    'id' => $identifier,
                    'label' => $block['name'] ?? $identifier,
                    'description' => $block['description'] ?? '',
                    'template' => [
                        'name' => $templateName,
                        'path' => $templatePath,
                    ],
                    'blocks' => [
                        [
                            'code' => $block['code'],
                            'name' => $block['name'] ?? $block['code'],
                            'tab' => $block['tab'] ?? '',
                            'icon' => $block['icon'] ?? '',
                        ],
                    ],
                ];
            }
        }

        return $layouts;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'prestashop.adapter.legacy.context' => LegacyContext::class,
        ]);
    }
}
