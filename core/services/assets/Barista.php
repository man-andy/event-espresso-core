<?php

namespace EventEspresso\core\services\assets;

use EventEspresso\core\domain\DomainInterface;

/**
 * @package EventEspresso\core\services\assets
 * @author  Manzoor Wani, Brent Christensen
 * @since   $VID:$
 */
class Barista
{

    const DEPENDENCY_LIST_REGISTERED = 'registered';

    const INLINE_SCRIPT_TARGET_HANDLE = 'hooks';


    /**
     * @var AssetManifest
     */
    private $asset_manifest;

    /**
     * @var DomainInterface
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $initialized = false;


    /**
     * Barista constructor.
     *
     * @param AssetManifest   $asset_manifest
     * @param DomainInterface $domain
     */
    public function __construct(AssetManifest $asset_manifest, DomainInterface $domain)
    {
        $this->asset_manifest = $asset_manifest;
        $this->domain         = $domain;
    }


    /**
     * @return void
     */
    public function initialize()
    {
        if (! $this->initialized) {
            add_action('wp_enqueue_scripts', [$this, 'registerScripts'], 0);
            add_action('admin_enqueue_scripts', [$this, 'registerScripts'], 0);
            add_action('wp_enqueue_scripts', [$this, 'registerPackagesStyles'], 0);
            add_action('admin_enqueue_scripts', [$this, 'registerPackagesStyles'], 0);
            add_action('wp_enqueue_scripts', [$this, 'addInlineData'], 10);
            add_action('admin_enqueue_scripts', [$this, 'addInlineData'], 10);
            $this->initialized = true;
        }
    }


    /**
     * Registers all the WordPress packages scripts that are in the standardized
     * `build/` location.
     *
     * @return void
     */
    public function registerScripts()
    {
        $entry_points = $this->asset_manifest->getEntryPoints();
        foreach ($entry_points as $entry_point) {
            if ($this->asset_manifest->hasAsset($entry_point)) {
                $handle = $this->asset_manifest->getAssetHandle($entry_point);
                $source = $this->asset_manifest->getAssetUrl($entry_point);
                $dependencies = $this->asset_manifest->getAssetDependencies($entry_point);
                $version = $this->asset_manifest->getAssetVersion($entry_point);
                wp_register_script($handle, $source, $dependencies, $version, true);
            }
        }
    }


    /**
     * Registers all the packages and domain styles that are in the build folder.
     *
     * @return void
     */
    public function registerPackagesStyles()
    {
        $entry_points = $this->asset_manifest->getEntryPoints();
        foreach ($entry_points as $entry_point) {
            if ($this->asset_manifest->hasAsset($entry_point, AssetManifest::ASSET_EXT_CSS)) {
                $handle = $this->asset_manifest->getAssetHandle($entry_point);
                $source = $this->asset_manifest->getAssetUrl($entry_point, AssetManifest::ASSET_EXT_CSS);
                $dependencies = $this->asset_manifest->getAssetDependencies($entry_point, AssetManifest::ASSET_EXT_CSS);
                $version = $this->asset_manifest->getAssetVersion($entry_point, AssetManifest::ASSET_EXT_CSS);
                wp_register_style($handle, $source, $dependencies, $version, 'all');
            }
        }
    }


    /**
     * @return void
     */
    public function addInlineData()
    {
        wp_add_inline_script(
            $this->asset_manifest->getAssetHandle(Barista::INLINE_SCRIPT_TARGET_HANDLE),
            sprintf('var baristaAssetsUrl = "%s";', $this->domain->distributionAssetsUrl()),
            'before'
        );
    }
}
