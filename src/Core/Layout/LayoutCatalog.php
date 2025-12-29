<?php

namespace PrestaSafe\PrettyBlocks\Core\Layout;

final class LayoutCatalog
{
    /**
     * Returns a static catalog describing available presets for the layout selector UI.
     */
    public static function getAll(): array
    {
        return [
            self::homeHeroWithProducts(),
            self::footerInformationColumns(),
            self::columnHighlight(),
        ];
    }

    public static function findByKey(string $key): ?array
    {
        foreach (self::getAll() as $layout) {
            if (($layout['key'] ?? '') === $key) {
                return $layout;
            }
        }

        return null;
    }

    private static function homeHeroWithProducts(): array
    {
        return [
            'key' => 'home-hero-slider-products',
            'label' => 'Accueil : slider et produits',
            'description' => 'Slider avec images de démonstration, bloc de titre, texte riche et sélection de produits.',
            'hooks' => ['displayHome'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_tiny_slider',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/tinyslider/default.tpl',
                    ],
                    'defaults' => [
                        'repeater' => [
                            [
                                'image' => ['url' => 'https://placehold.co/1110x522'],
                                'alt_image' => 'Slide 1',
                            ],
                            [
                                'image' => ['url' => 'https://placehold.co/1110x522'],
                                'alt_image' => 'Slide 2',
                            ],
                        ],
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h2',
                        'title' => 'Your title',
                        'classes' => '',
                    ],
                ],
                [
                    'order' => 3,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p> lorem ipsum </p>',
                    ],
                ],
                [
                    'order' => 4,
                    'code' => 'prettyblocks_featured_product',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/featured_products/default.tpl',
                    ],
                    'defaults' => [
                        'category' => null,
                        'number' => 8,
                        'title' => 'Our products',
                        'display_title' => true,
                        'display_link' => true,
                    ],
                ],
            ],
        ];
    }

    private static function footerInformationColumns(): array
    {
        return [
            'key' => 'footer-information-columns',
            'label' => 'Colonnes d\'informations pied de page',
            'description' => 'Deux colonnes texte pour le footer avec titres et contenus modifiables.',
            'hooks' => ['displayFooter', 'displayFooterBefore', 'displayFooterAfter'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h4',
                        'title' => 'Informations',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p> lorem ipsum </p>',
                    ],
                ],
                [
                    'order' => 3,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h4',
                        'title' => 'Aide & support',
                        'classes' => 'mt-4 mb-2',
                    ],
                ],
                [
                    'order' => 4,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p> lorem ipsum </p>',
                    ],
                ],
            ],
        ];
    }

    private static function columnHighlight(): array
    {
        return [
            'key' => 'sidebar-highlight',
            'label' => 'Colonne latérale : titre + texte',
            'description' => 'Bloc court pour colonnes (gauche/droite) avec un titre et un texte libre.',
            'hooks' => ['displayLeftColumn', 'displayRightColumn'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h3',
                        'title' => 'Your title',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p> lorem ipsum </p>',
                    ],
                ],
            ],
        ];
    }
}
