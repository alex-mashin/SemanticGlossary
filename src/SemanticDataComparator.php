<?php

namespace SG;

use SMW\Store;
use SMW\SemanticData;
use SMW\DIProperty;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author Stephan Gambke
 */
class SemanticDataComparator {

	/**
	 * @var Store
	 */
	private $store = null;

	/**
	 * @var SemanticData
	 */
	private $semanticData = null;

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param SemanticData $semanticData
	 */
	public function __construct( Store $store, SemanticData $semanticData ) {
		$this->store = $store;
		$this->semanticData = $semanticData;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $propertyId
	 *
	 * @return boolean
	 */
	public function compareForProperty( string $propertyId ): bool {

		[ $newEntries, $oldEntries ] = $this->lookupPropertyValues( $propertyId );

		if ( $this->hasNotSamePropertyValuesCount( $newEntries, $oldEntries ) ) {
			return true;
		}

		if ( $this->hasUnmatchPropertyValue( $newEntries, $oldEntries ) ) {
			return true;
		}

		return false;
	}

	private function lookupPropertyValues( string $propertyId ): array {

		$properties = $this->semanticData->getProperties();

		if ( array_key_exists( $propertyId, $properties ) ) {

			$newEntries = $this->semanticData->getPropertyValues( $properties[$propertyId] );
			$oldEntries = $this->store->getPropertyValues(
				$this->semanticData->getSubject(),
				$properties[$propertyId]
			);

			return array(
				$newEntries,
				$oldEntries
			);
		}

		$newEntries = array();
		$oldEntries = array();

		try{
			$property = new DIProperty( $propertyId );
		} catch ( \Exception $e ) {
			return array(
				$newEntries,
				$oldEntries
			);
		}

		$oldEntries = $this->store->getPropertyValues(
			$this->semanticData->getSubject(),
			$property
		);

		return [ $newEntries, $oldEntries ];
	}

	private function hasNotSamePropertyValuesCount( array $newEntries, array $oldEntries ) : bool {
		return count( $newEntries ) !== count( $oldEntries );
	}

	private function hasUnmatchPropertyValue( array $newEntries, array $oldEntries ): bool {

		foreach ( $newEntries as $newDi ) {
			$found = false;
			foreach ( $oldEntries as $oldKey => $oldDi ) {
				if ( $newDi->getHash() === $oldDi->getHash() ) {
					$found = true;
					unset( $oldEntries[$oldKey] );
					break;
				}
			}

			// If no match was possible...
			if ( !$found ) {
				return true;
			}
		}

		// Are there unmatched old entries left?
		if ( count( $oldEntries ) > 0 ) {
			return true;
		}

		return false;
	}

}
