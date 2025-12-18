<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasGermanSlug
{
	/**
	 * Convert German umlauts before slug generation.
	 */
	public static function bootHasGermanSlug(): void
	{
		static::saving(function ($model) {
			if ($model->isDirty('name') || empty($model->slug)) {
				$replacements = ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue', 'ß' => 'ss'];
				$name = str_replace(array_keys($replacements), array_values($replacements), $model->name);
				$model->slug = Str::slug($name);
			}
		});
	}
}
