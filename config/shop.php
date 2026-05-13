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

];
