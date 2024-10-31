<?php

namespace ACP\oEmbed;

use AMP_Base_Embed_Handler;
use AMP_HTML_Utils;
use DOMDocument;

class Infogram_Embed_Handler extends AMP_Base_Embed_Handler {
	public function register_embed() {
		add_filter( 'embed_oembed_html', [ $this, 'filter_embed_oembed_html' ], 10, 3 );
	}

	public function unregister_embed() {
		remove_filter( 'embed_oembed_html', [ $this, 'filter_embed_oembed_html' ] );
	}

	public function filter_embed_oembed_html( $cache, string $url, array $attr ) {
		$parsedUrl = wp_parse_url( $url );

		if ( ! isset( $parsedUrl['host'], $parsedUrl['path'] ) ) {
			return $cache;
		}

		if ( str_contains( $parsedUrl['host'], 'infogram.com' ) ) {
			if ( empty( $attr['height'] ) ) {
				return $cache;
			}

			$attributes = wp_array_slice_assoc( $attr, [ 'width', 'height' ] );

			if ( empty( $attr['width'] ) ) {
				$attributes['layout'] = 'fixed-height';
				$attributes['width']  = 'auto';
			} else {
				$attributes['layout'] = 'responsive';
			}

			$attributes['src']             = 'https://e.infogram.com/' . $this->get_infogram_id_from_html( $cache ) . '?src=embed';
			$attributes['sandbox']         = 'allow-scripts allow-same-origin allow-popups';
			$attributes['resizable']       = '';
			$attributes['allowfullscreen'] = '';

			$cache = AMP_HTML_Utils::build_tag(
				'amp-iframe',
				$attributes,
				sprintf(
					'<div style="visibility: hidden" overflow tabindex=0 role=button aria-label="%1$s" placeholder>%1$s</div>',
					__( 'Loading...', 'oembed-infogram' )
				)
			);
		}

		return $cache;
	}

	protected function get_infogram_id_from_html( string $html ) {
		$dom = new DOMDocument();
		@$dom->loadHTML( $html );
		$div = $dom->getElementsByTagName( 'div' )->item( 0 );

		return $div->getAttribute( 'data-id' );
	}
}
