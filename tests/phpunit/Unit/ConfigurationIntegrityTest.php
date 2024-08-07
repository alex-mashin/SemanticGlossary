<?php

namespace SG\Tests;

use SG\Cache\GlossaryCache;

/**
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ConfigurationIntegrityTest extends \PHPUnit_Framework_TestCase {

	public function testValidityOfCacheTypeSetting() {

		$instance = new GlossaryCache();

		if ( isset( $GLOBAL['wgexLingoCacheType'] ) ) {
			$this->assertCacheType( $GLOBAL['wgexLingoCacheType'] );
		}

		$this->assertCacheType( $instance->getCacheType() );
	}

	protected function assertCacheType( string $cacheType ) {
		$this->assertTrue( array_key_exists( $cacheType, $GLOBALS['wgObjectCaches'] ) );
	}

}
