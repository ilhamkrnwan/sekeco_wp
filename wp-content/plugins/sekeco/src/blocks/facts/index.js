/**
 * Facts block: the entry's own fields as a ledger, strip or row of links.
 * The front end reads the post meta; the editor shows a sample of the layout.
 */
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import './style.scss';
import metadata from './block.json';
import { blockIcon } from '../../shared/icon';
import { saveNull } from '../../shared/register';

const SAMPLE = [
	[ __( 'Client', 'sekeco' ), __( 'Studio name', 'sekeco' ) ],
	[ __( 'Role', 'sekeco' ), __( 'Identity, product', 'sekeco' ) ],
	[ __( 'Year', 'sekeco' ), '2026' ],
];

function Edit( { attributes, setAttributes } ) {
	const { layout } = attributes;
	const blockProps = useBlockProps( {
		className: `sk-facts sk-facts--${ layout } is-editor`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'sekeco' ) }>
					<SelectControl
						label={ __( 'Layout', 'sekeco' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Ledger (stacked rows)', 'sekeco' ),
								value: 'ledger',
							},
							{
								label: __( 'Strip (one row)', 'sekeco' ),
								value: 'strip',
							},
							{
								label: __( 'Links (pills)', 'sekeco' ),
								value: 'links',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
						help={ __(
							'Reads the fields of the entry it sits in. Empty fields are hidden.',
							'sekeco'
						) }
					/>
				</PanelBody>
			</InspectorControls>
			<dl { ...blockProps }>
				{ SAMPLE.map( ( [ term, value ] ) => (
					<div className="sk-facts__row" key={ term }>
						<dt>{ term }</dt>
						<dd>{ value }</dd>
					</div>
				) ) }
			</dl>
		</>
	);
}

registerBlockType( metadata.name, {
	icon: blockIcon( 'case-round' ),
	edit: Edit,
	save: saveNull,
} );
