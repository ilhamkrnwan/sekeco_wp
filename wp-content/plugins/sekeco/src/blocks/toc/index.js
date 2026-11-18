/**
 * On this page block: the front end builds the list from the entry's headings;
 * the editor shows a sample of it.
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './style.scss';
import metadata from './block.json';
import { blockIcon } from '../../shared/icon';
import { saveNull } from '../../shared/register';

const SAMPLE = [
	__( 'Overview', 'sekeco' ),
	__( 'Features', 'sekeco' ),
	__( 'Pricing', 'sekeco' ),
];

function Edit() {
	const blockProps = useBlockProps( { className: 'sk-toc is-editor' } );

	return (
		<nav { ...blockProps }>
			<p className="sk-toc__title">{ __( 'On this page', 'sekeco' ) }</p>
			<ol className="sk-toc__list">
				{ SAMPLE.map( ( label ) => (
					<li className="sk-toc__item" key={ label }>
						<a
							href="#sample"
							onClick={ ( e ) => e.preventDefault() }
						>
							{ label }
						</a>
					</li>
				) ) }
			</ol>
		</nav>
	);
}

registerBlockType( metadata.name, {
	icon: blockIcon( 'diagram-up' ),
	edit: Edit,
	save: saveNull,
} );
