<?php
/**
 * Genesis Starter Theme
 *
 * @package   SeoThemes\GenesisStarterTheme
 * @link      https://seothemes.com/genesis-starter-theme
 * @author    SEO Themes
 * @copyright Copyright © 2018 SEO Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\GenesisStarterTheme;

use SeoThemes\Core\AssetLoader;
use SeoThemes\Core\Constants;
use SeoThemes\Core\CustomColors;
use SeoThemes\Core\GenesisSettings;
use SeoThemes\Core\GoogleFonts;
use SeoThemes\Core\Hooks;
use SeoThemes\Core\ImageSizes;
use SeoThemes\Core\PageLayouts;
use SeoThemes\Core\PluginActivation;
use SeoThemes\Core\SimpleSocialIcons;
use SeoThemes\Core\TextDomain;
use SeoThemes\Core\ThemeSupport;
use SeoThemes\Core\WidgetArea;

$core_assets = [
	AssetLoader::SCRIPTS => [
		[
			AssetLoader::HANDLE   => 'menus',
			AssetLoader::URL      => AssetLoader::path( '/resources/js/menus.js' ),
			AssetLoader::DEPS     => [ 'jquery' ],
			AssetLoader::VERSION  => wp_get_theme()->get( 'Version' ),
			AssetLoader::FOOTER   => true,
			AssetLoader::ENQUEUE  => true,
			AssetLoader::LOCALIZE => [
				AssetLoader::LOCALIZEVAR  => 'genesis_responsive_menu',
				AssetLoader::LOCALIZEDATA => [
					'mainMenu'         => __( 'Menu', 'genesis-starter-theme' ),
					'subMenu'          => __( 'Sub Menu', 'genesis-starter-theme' ),
					'menuIconClass'    => null,
					'subMenuIconClass' => null,
					'menuClasses'      => [
						'combine' => [
							'.nav-primary',
							'.nav-secondary',
						],
					],
				]
			],
		],
		[
			AssetLoader::HANDLE  => 'script',
			AssetLoader::URL     => AssetLoader::path( '/resources/js/script.js' ),
			AssetLoader::DEPS    => [ 'jquery' ],
			AssetLoader::VERSION => wp_get_theme()->get( 'Version' ),
			AssetLoader::FOOTER  => true,
			AssetLoader::ENQUEUE => true,
		],
	],
];

$core_constants = [
	Constants::DEFINE => [
		'CHILD_THEME_NAME'    => wp_get_theme()->get( 'Name' ),
		'CHILD_THEME_URL'     => wp_get_theme()->get( 'ThemeURI' ),
		'CHILD_THEME_VERSION' => wp_get_theme()->get( 'Version' ),
		'CHILD_THEME_HANDLE'  => wp_get_theme()->get( 'TextDomain' ),
		'CHILD_THEME_AUTHOR'  => wp_get_theme()->get( 'Author' ),
		'CHILD_THEME_DIR'     => get_stylesheet_directory(),
		'CHILD_THEME_URI'     => get_stylesheet_directory_uri(),
	],
];

$core_custom_colors = [
	'background' => [
		'default' => '#ffffff',
		'output'  => [
			[
				'elements'   => [
					'body',
					'.site-container',
				],
				'properties' => [
					'background-color' => '%s',
				],
			],
		],
	],
	'link'       => [
		'default' => '#0073e5',
		'output'  => [
			[
				'elements'   => [
					'a',
					'.entry-title a:focus',
					'.entry-title a:hover',
					'.genesis-nav-menu a:focus',
					'.genesis-nav-menu a:hover',
					'.genesis-nav-menu .current-menu-item > a',
					'.genesis-nav-menu .sub-menu .current-menu-item > a:focus',
					'.genesis-nav-menu .sub-menu .current-menu-item > a:hover',
					'.menu-toggle:focus',
					'.menu-toggle:hover',
					'.sub-menu-toggle:focus',
					'.sub-menu-toggle:hover',
				],
				'properties' => [
					'color' => '%s',
				],
			],
		],
	],
	'accent'     => [
		'default' => '#0073e5',
		'output'  => [
			[
				'elements'   => [
					'button:focus',
					'button:hover',
					'input[type="button"]:focus',
					'input[type="button"]:hover',
					'input[type="reset"]:focus',
					'input[type="reset"]:hover',
					'input[type="submit"]:focus',
					'input[type="submit"]:hover',
					'input[type="reset"]:focus',
					'input[type="reset"]:hover',
					'input[type="submit"]:focus',
					'input[type="submit"]:hover',
					'.button:focus',
					'.button:hover',
					'.genesis-nav-menu > .menu-highlight > a:hover',
					'.genesis-nav-menu > .menu-highlight > a:focus',
					'.genesis-nav-menu > .menu-highlight.current-menu-item > a',
				],
				'properties' => [
					'background-color' => '%s',
				],
			],
		],
	],
];

$core_example = [
	Example::SUB_CONFIG => [
		Example::KEY => 'value',
	],
];

$core_genesis_settings = [
	GenesisSettings::DEFAULTS => [
		GenesisSettings::SITE_LAYOUT => 'full-width-content',
	],
];

$core_google_fonts = [
	GoogleFonts::ENQUEUE => [
		'Source+Sans+Pro:400,600,700',
	],
];

$core_hooks = [
	Hooks::ADD    => [
		[
			Hooks::TAG         => 'genesis_site_title',
			Hooks::CALLBACK    => 'the_custom_logo',
			Hooks::PRIORITY    => 0,
			Hooks::CONDITIONAL => function () {
				return has_custom_logo();
			}
		],
		[
			Hooks::TAG      => 'genesis_markup_title-area_close',
			Hooks::CALLBACK => function ( $close_html ) {
				if ( $close_html ) {
					ob_start();
					do_action( 'child_theme_after_title_area' );
					$close_html = $close_html . ob_get_clean();
				}

				return $close_html;
			}
		],
		[
			Hooks::TAG      => 'genesis_before',
			Hooks::CALLBACK => function () {
				$wraps = get_theme_support( 'genesis-structural-wraps' );
				foreach ( $wraps[0] as $context ) {
					add_filter( "genesis_structural_wrap-{$context}", function ( $output, $original ) use ( $context ) {
						$position = ( 'open' === $original ) ? 'before' : 'after';
						ob_start();
						do_action( "child_theme_{$position}_{$context}_wrap" );
						if ( 'open' === $original ) {
							return ob_get_clean() . $output;
						} else {
							return $output . ob_get_clean();
						}
					}, 10, 2 );
				}
			}
		],
		[
			Hooks::TAG      => 'genesis_attr_content-sidebar-wrap',
			Hooks::CALLBACK => function ( $atts ) {
				$atts['class'] = 'wrap';

				return $atts;
			},
		],
		[
			Hooks::TAG      => 'admin_init',
			Hooks::CALLBACK => function () {
				add_editor_style( 'editor.css' );
			},
		],
		[
			Hooks::TAG      => 'child_theme_after_title_area',
			Hooks::CALLBACK => 'genesis_do_nav',
		],
		[
			Hooks::TAG      => 'child_theme_after_header_wrap',
			Hooks::CALLBACK => 'genesis_do_subnav',
		],
		[
			Hooks::TAG      => 'child_theme_before_footer_wrap',
			Hooks::CALLBACK => 'genesis_footer_widget_areas',
		],
	],
	Hooks::REMOVE => [
		[
			Hooks::TAG      => 'genesis_after_header',
			Hooks::CALLBACK => 'genesis_do_nav',
		],
		[
			Hooks::TAG      => 'genesis_after_header',
			Hooks::CALLBACK => 'genesis_do_subnav',
		],
		[
			Hooks::TAG      => 'genesis_before_footer',
			Hooks::CALLBACK => 'genesis_footer_widget_areas',
		],
	],
];

$core_image_sizes = [
	ImageSizes::ADD => [
		'featured' => [
			'width'  => 620,
			'height' => 380,
			'crop'   => true,
		],
		'hero'     => [
			'width'  => 1280,
			'height' => 720,
			'crop'   => true,
		],
	],
];

$core_layouts = [
	PageLayouts::UNREGISTER => [
		// PageLayouts::CONTENT_SIDEBAR,
		// PageLayouts::SIDEBAR_CONTENT,
		// PageLayouts::FULL_WIDTH_CONTENT,
		PageLayouts::CONTENT_SIDEBAR_SIDEBAR,
		PageLayouts::SIDEBAR_SIDEBAR_CONTENT,
		PageLayouts::SIDEBAR_CONTENT_SIDEBAR,
	]
];

$core_plugins = [
	PluginActivation::REGISTER => [
		[
			PluginActivation::NAME     => 'Genesis Widget Column Classes',
			PluginActivation::SLUG     => 'genesis-widget-column-classes',
			PluginActivation::REQUIRED => false,
		],
		[
			PluginActivation::NAME     => 'Icon Widget',
			PluginActivation::SLUG     => 'icon-widget',
			PluginActivation::REQUIRED => false,
		],
		[
			PluginActivation::NAME     => 'One Click Demo Import',
			PluginActivation::SLUG     => 'one-click-demo-import',
			PluginActivation::REQUIRED => false,
		],
		[
			PluginActivation::NAME     => 'Simple Social Icons',
			PluginActivation::SLUG     => 'simple-social-icons',
			PluginActivation::REQUIRED => false,
		],
	],
];

$core_simple_social_icons = [
	SimpleSocialIcons::DEFAULTS => [
		SimpleSocialIcons::NEW_WINDOW => 1,
		SimpleSocialIcons::SIZE       => 40,
	],
];

$core_textdomain = [
	TextDomain::DOMAIN => 'genesis-starter-theme',
];

$core_theme_support = [
	ThemeSupport::ADD => [
		'align-wide',
		'automatic-feed-links',
		'custom-logo'              => [
			'height'      => 100,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => [
				'.site-title',
				'.site-description',
			],
		],
		'custom-header'            => [
			'header-selector' => '.hero-section',
			'default_image'   => get_stylesheet_directory_uri() . '/resources/img/hero.jpg',
			'header-text'     => false,
			'width'           => 1280,
			'height'          => 720,
			'flex-height'     => true,
			'flex-width'      => true,
			'uploads'         => true,
			'video'           => true,
		],
		'genesis-accessibility'    => [
			'404-page',
			'drop-down-menu',
			'headings',
			'rems',
			'search-form',
			'skip-links',
		],
		'genesis-after-entry-widget-area',
		'genesis-footer-widgets'   => 4,
		'genesis-menus'            => [
			'primary'   => __( 'Header Menu', 'genesis-starter-theme' ),
			'secondary' => __( 'After Header Menu', 'genesis-starter-theme' ),
		],
		'genesis-responsive-viewport',
		'genesis-structural-wraps' => [
			'header',
			'menu-secondary',
			'footer-widgets',
			'footer',
		],
		'gutenberg'                => [
			'wide-images' => true,
		],
		'html5'                    => [
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'search-form',
		],
		'post-thumbnails',
		'woocommerce',
		'wc-product-gallery-zoom',
		'wc-product-gallery-lightbox',
		'wc-product-gallery-slider',
		'wp-block-styles',
	],
];

$core_widget_areas = [
	WidgetArea::UNREGISTER => [
		WidgetArea::HEADER_RIGHT,
		WidgetArea::SIDEBAR_ALT,
	],
];

return [
	AssetLoader::class       => $core_assets,
	Constants::class         => $core_constants,
	CustomColors::class      => $core_custom_colors,
	Example::class           => $core_example,
	GenesisSettings::class   => $core_genesis_settings,
	GoogleFonts::class       => $core_google_fonts,
	Hooks::class             => $core_hooks,
	ImageSizes::class        => $core_image_sizes,
	PageLayouts::class       => $core_layouts,
	PluginActivation::class  => $core_plugins,
	SimpleSocialIcons::class => $core_simple_social_icons,
	TextDomain::class        => $core_textdomain,
	ThemeSupport::class      => $core_theme_support,
	WidgetArea::class        => $core_widget_areas,
];
