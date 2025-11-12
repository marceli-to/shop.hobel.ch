<?php

namespace App\View\Components\Media;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Image component with automatic format conversion (AVIF, WebP, JPEG).
 *
 * Usage examples:
 *
 * Basic usage:
 * <x-media.image src="products/image.jpg" alt="Product" :width="800" />
 *
 * With custom formats:
 * <x-media.image
 *     src="products/image.jpg"
 *     alt="Product"
 *     :width="400"
 *     :height="300"
 *     fit="crop"
 *     :quality="90"
 *     :formats="['avif', 'webp', 'jpg']"
 * />
 *
 * With custom class and eager loading:
 * <x-media.image
 *     src="products/image.jpg"
 *     alt="Hero Image"
 *     :width="1200"
 *     class="rounded-lg shadow-xl"
 *     loading="eager"
 * />
 */
class Image extends Component
{
	/**
	 * The image source path.
	 */
	public string $src;

	/**
	 * The image alt text.
	 */
	public string $alt;

	/**
	 * The image width.
	 */
	public ?int $width;

	/**
	 * The image height.
	 */
	public ?int $height;

	/**
	 * The fit mode (crop, contain, fill, etc.).
	 */
	public string $fit;

	/**
	 * The image quality (0-100).
	 */
	public int $quality;

	/**
	 * The formats to generate (avif, webp, jpg).
	 */
	public array $formats;

	/**
	 * Additional CSS classes.
	 */
	public string $class;

	/**
	 * Loading strategy (lazy, eager).
	 */
	public string $loading;

	/**
	 * Create a new component instance.
	 */
	public function __construct(
		string $src,
		string $alt = '',
		?int $width = null,
		?int $height = null,
		string $fit = 'crop',
		int $quality = 85,
		array $formats = ['avif', 'webp', 'jpg'],
		string $class = '',
		string $loading = 'lazy'
	) {
		$this->src = $src;
		$this->alt = $alt;
		$this->width = $width;
		$this->height = $height;
		$this->fit = $fit;
		$this->quality = $quality;
		$this->formats = $formats;
		$this->class = $class;
		$this->loading = $loading;
	}

	/**
	 * Build the image URL with parameters.
	 */
	public function buildUrl(string $format = null): string
	{
		$params = [];

		if ($this->width) {
			$params[] = 'w=' . $this->width;
		}

		if ($this->height) {
			$params[] = 'h=' . $this->height;
		}

		if ($this->fit) {
			$params[] = 'fit=' . $this->fit;
		}

		if ($format) {
			$params[] = 'fm=' . $format;
		}

		$params[] = 'q=' . $this->quality;

		$queryString = implode('&', $params);

		return '/img/' . $this->src . ($queryString ? '?' . $queryString : '');
	}

	/**
	 * Get MIME type for format.
	 */
	public function getMimeType(string $format): string
	{
		return match ($format) {
			'avif' => 'image/avif',
			'webp' => 'image/webp',
			'jpg', 'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			default => 'image/jpeg',
		};
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.media.image');
	}
}
