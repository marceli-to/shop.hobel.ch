<?php

return [

  /**
   * Configurable product pricing globals
   */

  'configurable' => [
    /**
     * Raw board thickness in metres, used in the material cost formula
     * (Holzpreis × Fläche × Rohdicke × Sortier-/Verschnittfaktor).
     */
    'raw_thickness_m' => 0.040,

    /**
     * Rounding step in CHF applied to the final price.
     */
    'price_rounding_step' => 50,
  ],

  /**
   * Default SEO / OpenGraph metadata.
   *
   * Used for static pages (landing, basket, checkout) and as a fallback
   * when a category or product has no meta description / image of its own.
   */
  'meta' => [
    'description' => env('SHOP_META_DESCRIPTION', 'Hochwertige Tische und Holzprodukte aus der Schweiz – individuell konfigurierbar und massgefertigt.'),

    /**
     * Static fallback OpenGraph image (path relative to the public root,
     * resolved to an absolute URL). Should be ~1200×630px for social cards.
     */
    'og_image' => env('SHOP_META_OG_IMAGE', '/og_image.jpg'),
  ],

];
