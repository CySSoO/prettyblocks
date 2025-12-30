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
            self::homeSliderAndIntro(),
            self::homeFeaturedGrid(),
            self::homeStoryWithImage(),
            self::homeFaqShowcase(),
            self::topAnnouncementBar(),
            self::navFullWidthHero(),
            self::leftColumnImagePromo(),
            self::rightColumnFaq(),
            self::footerColumnsWithCta(),
            self::footerServiceDetails(),
            self::cmsContentHighlight(),
            self::cmsContentWithFaq(),
            self::categoryIntro(),
            self::categoryFooterFaq(),
            self::productDescriptionStack(),
            self::productSummaryHighlight(),
            self::productFaqSupport(),
            self::productRelatedShowcase(),
        ];
    }

    public static function getTargetedHooks(): array
    {
        $hooks = [];

        foreach (self::getAll() as $layout) {
            foreach ($layout['hooks'] as $hook) {
                $hooks[$hook] = true;
            }
        }

        return array_keys($hooks);
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

    private static function homeSliderAndIntro(): array
    {
        return [
            'key' => 'home-slider-intro',
            'label' => 'Accueil : slider et introduction',
            'description' => 'Slider pleine largeur suivi d\'un titre et d\'un texte de présentation.',
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
                                'image' => ['url' => 'https://placehold.co/1200x480'],
                                'alt_image' => 'Nouvelle collection',
                            ],
                            [
                                'image' => ['url' => 'https://placehold.co/1200x480'],
                                'alt_image' => 'Offres du moment',
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
                        'title' => 'Bienvenue en boutique',
                        'classes' => 'text-center my-4',
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
                        'content' => '<p class="text-center">Découvrez notre sélection de nouveautés et profitez d\'expériences personnalisées.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function homeFeaturedGrid(): array
    {
        return [
            'key' => 'home-featured-grid',
            'label' => 'Accueil : produits mis en avant',
            'description' => 'Titre, texte descriptif et sélection de produits pour la page d\'accueil.',
            'hooks' => ['displayHome'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h2',
                        'title' => 'Sélection du moment',
                        'classes' => 'text-center mb-3',
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
                        'content' => '<p class="text-center">Un aperçu rapide de nos meilleures ventes pour inspirer vos visiteurs.</p>',
                    ],
                ],
                [
                    'order' => 3,
                    'code' => 'prettyblocks_featured_product',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/featured_products/default.tpl',
                    ],
                    'defaults' => [
                        'category' => null,
                        'number' => 12,
                        'title' => 'Coup de cœur',
                        'display_title' => true,
                        'display_link' => true,
                    ],
                ],
            ],
        ];
    }

    private static function homeStoryWithImage(): array
    {
        return [
            'key' => 'home-story-image',
            'label' => 'Accueil : image et storytelling',
            'description' => 'Image pleine largeur, titre et texte narratif pour introduire la marque.',
            'hooks' => ['displayHome'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_custom_image_block',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/customimage/default.tpl',
                    ],
                    'defaults' => [
                        'image' => ['url' => 'https://placehold.co/1280x520'],
                        'alignment' => 'center',
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
                        'title' => 'Notre histoire',
                        'classes' => 'text-center mt-4',
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
                        'content' => '<p class="text-center">Présentez votre univers, vos engagements et ce qui rend votre offre unique.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function homeFaqShowcase(): array
    {
        return [
            'key' => 'home-faq-showcase',
            'label' => 'Accueil : FAQ et rassurance',
            'description' => 'Une section FAQ pour répondre aux questions clés directement sur la page d\'accueil.',
            'hooks' => ['displayHome'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h2',
                        'title' => 'Questions fréquentes',
                        'classes' => 'text-center mb-3',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_faq',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/faq/default.tpl',
                    ],
                    'defaults' => [
                        'title' => 'FAQ',
                        'repeater' => [
                            ['question' => 'Quels sont vos délais de livraison ?', 'answer' => '<p>Nous livrons sous 48h en France métropolitaine.</p>'],
                            ['question' => 'Puis-je retourner un produit ?', 'answer' => '<p>Les retours sont possibles sous 30 jours.</p>'],
                            ['question' => 'Où suivre ma commande ?', 'answer' => '<p>Depuis votre espace client, rubrique &ldquo;Mes commandes&rdquo;.</p>'],
                        ],
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
                        'content' => '<p class="text-center">Besoin d\'aide supplémentaire ? <a href="#contact">Contactez notre équipe</a>.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function topAnnouncementBar(): array
    {
        return [
            'key' => 'top-announcement',
            'label' => 'Bandeau haut de page : annonce',
            'description' => 'Titre court et message informatif pour le hook displayTop.',
            'hooks' => ['displayTop'],
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
                        'title' => 'Livraison offerte dès 49€',
                        'classes' => 'text-center mb-1',
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
                        'content' => '<p class="text-center mb-0">Profitez de nos offres du moment et des retours simplifiés.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function navFullWidthHero(): array
    {
        return [
            'key' => 'nav-full-hero',
            'label' => 'Bannière navigation pleine largeur',
            'description' => 'Slider principal pour le hook displayNavFullWidth.',
            'hooks' => ['displayNavFullWidth'],
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
                            ['image' => ['url' => 'https://placehold.co/1440x500'], 'alt_image' => 'Collection premium'],
                            ['image' => ['url' => 'https://placehold.co/1440x500'], 'alt_image' => 'Nouveautés saison'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function leftColumnImagePromo(): array
    {
        return [
            'key' => 'left-column-image-promo',
            'label' => 'Colonne gauche : visuel et texte',
            'description' => 'Image promotionnelle et paragraphe pour encourager la découverte.',
            'hooks' => ['displayLeftColumn'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_custom_image_block',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/customimage/default.tpl',
                    ],
                    'defaults' => [
                        'image' => ['url' => 'https://placehold.co/400x400'],
                        'alignment' => 'center',
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
                        'content' => '<p class="text-center">Une sélection exclusive pour compléter votre panier.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function rightColumnFaq(): array
    {
        return [
            'key' => 'right-column-faq',
            'label' => 'Colonne droite : aide rapide',
            'description' => 'Mini FAQ pour rassurer dans les colonnes latérales.',
            'hooks' => ['displayRightColumn'],
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
                        'title' => 'Besoin d\'aide ?',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_faq',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/faq/default.tpl',
                    ],
                    'defaults' => [
                        'title' => 'FAQ express',
                        'repeater' => [
                            ['question' => 'Paiement sécurisé ?', 'answer' => '<p>Oui, toutes les transactions sont cryptées.</p>'],
                            ['question' => 'Retrait en boutique ?', 'answer' => '<p>Retrait gratuit disponible selon les magasins.</p>'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function footerColumnsWithCta(): array
    {
        return [
            'key' => 'footer-columns-cta',
            'label' => 'Pied de page : colonnes + CTA',
            'description' => 'Deux colonnes éditoriales avec un appel à l\'action bas de page.',
            'hooks' => ['displayFooter', 'displayFooterBefore'],
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
                        'title' => 'À propos',
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
                        'content' => '<p>Présentez brièvement votre marque et les points forts de votre offre.</p>',
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
                        'title' => 'Contact',
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
                        'content' => '<p>Email : contact@example.com<br/>Téléphone : 01 23 45 67 89</p>',
                    ],
                ],
                [
                    'order' => 5,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p class="text-center mt-3"><a href="#newsletter" class="btn btn-primary">Inscrivez-vous à la newsletter</a></p>',
                    ],
                ],
            ],
        ];
    }

    private static function footerServiceDetails(): array
    {
        return [
            'key' => 'footer-service-details',
            'label' => 'Pied de page : services et garanties',
            'description' => 'Bloc multi-colonnes pour présenter services, garanties et horaires.',
            'hooks' => ['displayFooter', 'displayFooterAfter'],
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
                        'title' => 'Services clients',
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
                        'content' => '<p>Support 6j/7, réponses sous 24h, retours gratuits.</p>',
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
                        'title' => 'Horaires',
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
                        'content' => '<p>Lun - Ven : 9h - 19h<br/>Samedi : 10h - 18h</p>',
                    ],
                ],
            ],
        ];
    }

    private static function cmsContentHighlight(): array
    {
        return [
            'key' => 'cms-content-highlight',
            'label' => 'CMS : bloc contenu',
            'description' => 'Titre et contenu CMS pour enrichir les pages de type CMS.',
            'hooks' => ['displayCMSDisputeInformation'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h2',
                        'title' => 'Informations',
                        'classes' => 'mb-3',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_cms_content',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/cms/cms_content_block.tpl',
                    ],
                    'defaults' => [],
                ],
            ],
        ];
    }

    private static function cmsContentWithFaq(): array
    {
        return [
            'key' => 'cms-content-faq',
            'label' => 'CMS : contenu + FAQ',
            'description' => 'Combine le contenu CMS avec une foire aux questions pour clarifier les informations.',
            'hooks' => ['displayCMSPrintButton'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_cms_content',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/cms/cms_content_block.tpl',
                    ],
                    'defaults' => [],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_faq',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/faq/default.tpl',
                    ],
                    'defaults' => [
                        'title' => 'Questions fréquentes',
                        'repeater' => [
                            ['question' => 'Comment utiliser ce service ?', 'answer' => '<p>Ajoutez les informations clés ici.</p>'],
                            ['question' => 'Qui contacter en cas de souci ?', 'answer' => '<p>Indiquez l\'email ou le numéro dédié.</p>'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function categoryIntro(): array
    {
        return [
            'key' => 'category-intro',
            'label' => 'Catégorie : titre et description',
            'description' => 'En-tête de catégorie avec titre éditorial et description riche.',
            'hooks' => ['displayCategoryTop'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_title',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/title/default.tpl',
                    ],
                    'defaults' => [
                        'tag' => 'h1',
                        'title' => 'Titre de catégorie',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_category_description',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/category/category_description_block.tpl',
                    ],
                    'defaults' => [],
                ],
            ],
        ];
    }

    private static function categoryFooterFaq(): array
    {
        return [
            'key' => 'category-footer-faq',
            'label' => 'Catégorie : FAQ bas de page',
            'description' => 'Section FAQ ciblée pour les catégories, idéale pour le bas de page.',
            'hooks' => ['displayCategoryFooter'],
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
                        'title' => 'Infos utiles',
                        'classes' => 'text-center mb-3',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_faq',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/faq/default.tpl',
                    ],
                    'defaults' => [
                        'title' => 'FAQ catégorie',
                        'repeater' => [
                            ['question' => 'Quels produits recommandez-vous ?', 'answer' => '<p>Mettez en avant les best-sellers de la catégorie.</p>'],
                            ['question' => 'Y a-t-il des offres spéciales ?', 'answer' => '<p>Ajoutez ici vos conditions promotionnelles.</p>'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function productDescriptionStack(): array
    {
        return [
            'key' => 'product-description-stack',
            'label' => 'Produit : description complète',
            'description' => 'Titre et description détaillée du produit pour enrichir la fiche.',
            'hooks' => ['displayProductExtraContent'],
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
                        'title' => 'Détails du produit',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_product_description',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/products/product_description_block.tpl',
                    ],
                    'defaults' => [],
                ],
            ],
        ];
    }

    private static function productSummaryHighlight(): array
    {
        return [
            'key' => 'product-summary-highlight',
            'label' => 'Produit : points clés',
            'description' => 'Résumé court et paragraphe additionnel dans la fiche produit.',
            'hooks' => ['displayProductAdditionalInfo'],
            'blocks' => [
                [
                    'order' => 1,
                    'code' => 'prettyblocks_product_description_short',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/products/product_description_short_block.tpl',
                    ],
                    'defaults' => [],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_custom_text',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/custom_text/default.tpl',
                    ],
                    'defaults' => [
                        'content' => '<p>Livraison rapide, retours simplifiés et assistance dédiée.</p>',
                    ],
                ],
            ],
        ];
    }

    private static function productFaqSupport(): array
    {
        return [
            'key' => 'product-faq-support',
            'label' => 'Produit : support & FAQ',
            'description' => 'FAQ dédiée aux produits pour lever les doutes avant l\'achat.',
            'hooks' => ['displayFooterProduct'],
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
                        'title' => 'Besoin de précisions ?',
                        'classes' => 'text-center mb-3',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_faq',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/faq/default.tpl',
                    ],
                    'defaults' => [
                        'title' => 'Questions sur le produit',
                        'repeater' => [
                            ['question' => 'Quelles sont les dimensions ?', 'answer' => '<p>Ajoutez ici les mesures principales.</p>'],
                            ['question' => 'Comment entretenir le produit ?', 'answer' => '<p>Indiquez les conseils d\'utilisation et d\'entretien.</p>'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function productRelatedShowcase(): array
    {
        return [
            'key' => 'product-related-showcase',
            'label' => 'Produit : produits associés',
            'description' => 'Mise en avant de produits liés ou d\'une catégorie associée.',
            'hooks' => ['displayProductExtraContent'],
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
                        'title' => 'Complétez votre panier',
                        'classes' => 'mb-2',
                    ],
                ],
                [
                    'order' => 2,
                    'code' => 'prettyblocks_featured_product',
                    'template' => [
                        'name' => 'default',
                        'path' => 'module:prettyblocks/views/templates/blocks/featured_products/default.tpl',
                    ],
                    'defaults' => [
                        'category' => null,
                        'number' => 4,
                        'title' => 'Produits recommandés',
                        'display_title' => true,
                        'display_link' => false,
                    ],
                ],
            ],
        ];
    }
}
