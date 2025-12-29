<p align="center">
<img src="https://user-images.githubusercontent.com/4788787/205036810-aa5dcd65-d7d4-4728-9284-1201a6067cb4.png" alt="PrettyBlocks logo">
</p>

# PrettyBlocks
<br>

![Téléchargements](https://img.shields.io/github/downloads/PrestaSafe/prettyblocks/total)

Le premier page builder open source conçu pour PrestaShop ! 

La documentation technique est disponible ici : [https://prettyblocks.io/](https://prettyblocks.io/): 


## Installation

> L'installation du module à partir de GitHub nécessite l'installation de nodejs et npm.

### Manuellement

```
cd modules
git clone https://github.com/PrestaSafe/prettyblocks.git
cd prettyblocks
composer install
cd _dev
npm install && npm run build
cd ../../../ && php bin/console prestashop:module install prettyblocks
```

### Release

Téléchargez la [dernière release](https://github.com/PrestaSafe/prettyblocks/releases/latest), puis installez le module directement dans votre PrestaShop :) 

Vous pouvez également utiliser notre module ClassicBlocks, afin d'avoir 4 blocks à utiliser. Disponible ici: [Télécharger ClassicBlocks](https://github.com/PrestaSafe/classicblocks)

## Themes compatible

Découvrez notre [Theme Optimisé SEO / Performance et mobile PrestaShop: CartZilla](https://www.prestasafe.com/product/theme-prestashop-cartzilla) compatible avec PrettyBlocks !


## English version

PrettyBlocks is the first page builder open source for PrestaShop

You will find the technical documentation here (only in French, soon in english too): [Technical documentation](https://prettyblocks.io/)

## Installation

> In order to install the module from GitHub source, you must have node and npm available on your computer.

### Manually

```
cd modules
git clone https://github.com/PrestaSafe/prettyblocks.git
cd prettyblocks
composer install
cd _dev
npm install && npm run build
cd ../../../ && php bin/console prestashop:module install prettyblocks
```

### From Release
For installing PrettyBlocks, you can download our [latest release](https://github.com/PrestaSafe/prettyblocks/releases/latest) and install the module directly in your PrestaShop.

You can also add our module ClassicBlocks for having 4 blocks to use [Download ClassicBlocks](https://github.com/PrestaSafe/classicblocks)

## Compatible Themes

Discover our [PrestaShop SEO / Performance and Mobile Optimized Theme: CartZilla](https://www.prestasafe.com/product/theme-prestashop-cartzilla) compatible PrettyBlocks !

## Prévisualisation des layouts

Vous pouvez tester un preset de layout avant de l'appliquer définitivement sur un hook :

1. Depuis l'UI Layouts, choisissez un preset et lancez la **prévisualisation**. Les blocs du preset sont instanciés avec un flag temporaire et rendus via `views/templates/front/zone.tpl` pour refléter le rendu final.
2. Si le rendu vous convient, cliquez sur **Confirmer** : le flag temporaire est retiré et les blocs deviennent persistants dans la zone ciblée.
3. Si vous souhaitez annuler, utilisez l'action **Annuler** qui supprime uniquement les blocs marqués comme temporaires sans toucher aux blocs existants.

Ces actions permettent de préparer un layout sans impacter immédiatement le front-office et de valider les changements une fois la prévisualisation validée.

## Top contributors 
<p>Thanks to</p>
<a href="https://github.com/PrestaSafe/prettyblocks/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=PrestaSafe/prettyblocks" />
</a>


## License

This module is released under AFL license.
See [License](/LICENSE.md) for details.

Enjoy 😉
