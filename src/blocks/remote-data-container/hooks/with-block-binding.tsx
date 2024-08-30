import { InspectorControls } from '@wordpress/block-editor';
import { BlockEditProps } from '@wordpress/blocks';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useContext, useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { BlockBindingControls } from '@/blocks/remote-data-container/components/block-binding-controls';
import { REMOTE_DATA_CONTEXT_KEY } from '@/blocks/remote-data-container/config/constants';
import { LoopIndexContext } from '@/blocks/remote-data-container/context/loop-index-context';
import {
	BLOCK_BINDING_SOURCE,
	PATTERN_OVERRIDES_BINDING_SOURCE,
	PATTERN_OVERRIDES_CONTEXT_KEY,
} from '@/config/constants';
import { getMismatchedAttributes } from '@/utils/block-binding';
import { getBlockAvailableBindings } from '@/utils/localized-block-data';

interface BoundBlockEditProps {
	attributes: RemoteDataInnerBlockAttributes;
	availableBindings: AvailableBindings;
	blockName: string;
	children: JSX.Element;
	remoteDataName: string;
	setAttributes: ( attributes: RemoteDataInnerBlockAttributes ) => void;
}

function BoundBlockEdit( props: BoundBlockEditProps ) {
	const { attributes, availableBindings, blockName, remoteDataName, setAttributes } = props;
	const existingBindings = attributes.metadata?.bindings ?? {};

	function removeBinding( target: string ) {
		const { [ target ]: _remove, ...newBindings } = existingBindings;
		setAttributes( {
			metadata: {
				...attributes.metadata,
				bindings: newBindings,
				name: undefined,
			},
		} );
	}

	function updateBinding( target: string, args: Omit< RemoteDataBlockBindingArgs, 'name' > ) {
		setAttributes( {
			metadata: {
				...attributes.metadata,
				bindings: {
					...attributes.metadata?.bindings,
					[ target ]: {
						source: BLOCK_BINDING_SOURCE,
						args: {
							...args,
							name: remoteDataName, // Remote Data Block name
						},
					},
				},
				name: availableBindings[ args.field ]?.name, // Changes block name in list view.
			},
		} );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Remote Data', 'remote-data-blocks' ) }>
					<BlockBindingControls
						attributes={ attributes }
						availableBindings={ availableBindings }
						blockName={ blockName }
						removeBinding={ removeBinding }
						updateBinding={ updateBinding }
					/>
				</PanelBody>
			</InspectorControls>
			{ props.children }
		</>
	);
}

export const withBlockBinding = createHigherOrderComponent( BlockEdit => {
	return ( props: BlockEditProps< RemoteDataInnerBlockAttributes > ) => {
		const { attributes, context, name, setAttributes } = props;
		const remoteData = context[ REMOTE_DATA_CONTEXT_KEY ] as RemoteData | undefined;
		const availableBindings = getBlockAvailableBindings( remoteData?.blockName ?? '' );
		const hasAvailableBindings = Boolean( Object.keys( availableBindings ).length );

		// If the block does not have a remote data context, render it as usual.
		if ( ! remoteData || ! hasAvailableBindings ) {
			return <BlockEdit { ...props } />;
		}

		// Synced pattern overrides are provided via context and the value can be:
		//
		// - undefined (block is not in a synced pattern)
		// - an empty array (block is in a synced pattern, but no overrides are applied)
		// - an object defining the applied overrides
		//
		// This gives no indication of whether overrides are enabled or not. For
		// that, we need to check the block's metadata bindings for the pattern
		// overrides binding source.
		//
		// This seems likely to change, so the code here may need maintenance. For
		// our purposes, though, we just want to know whether the block is in a
		// synced pattern and whether overrides are enabled. Trying to update
		// a synced block without overrides enabled is useless and can cause issues.

		const patternOverrides = context[ PATTERN_OVERRIDES_CONTEXT_KEY ] as string[] | undefined;
		const { index } = useContext( LoopIndexContext );
		const isInSyncedPattern = Boolean( patternOverrides );
		const hasEnabledOverrides = Object.values( attributes.metadata?.bindings ?? {} ).some(
			binding => binding.source === PATTERN_OVERRIDES_BINDING_SOURCE
		);

		// If the block is not writable, render it as usual.
		if ( isInSyncedPattern && ! hasEnabledOverrides ) {
			return <BlockEdit { ...props } />;
		}

		// If the block has a binding and the attributes do not match their expected
		// values, update and merge the attributes.
		const mergedAttributes = useMemo< RemoteDataInnerBlockAttributes >( () => {
			return {
				...attributes,
				...getMismatchedAttributes( attributes, remoteData.results, index ),
			};
		}, [ attributes, remoteData.results, index ] );

		return (
			<BoundBlockEdit
				attributes={ mergedAttributes }
				availableBindings={ availableBindings }
				blockName={ name }
				remoteDataName={ remoteData?.blockName ?? '' }
				setAttributes={ setAttributes }
			>
				<BlockEdit { ...props } attributes={ mergedAttributes } />
			</BoundBlockEdit>
		);
	};
}, 'withBlockBinding' );