/**
 * Native document settings panels for Works, Portfolio, and Team meta.
 */
import {
	SelectControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';

const usePostMeta = () => {
	const { postType, meta } = useSelect( ( select ) => {
		const editor = select( 'core/editor' );

		return {
			postType: editor.getCurrentPostType(),
			meta: editor.getEditedPostAttribute( 'meta' ) || {},
		};
	}, [] );

	const { editPost } = useDispatch( 'core/editor' );

	const setMeta = ( key, value ) => {
		editPost( {
			meta: {
				...meta,
				[ key ]: value,
			},
		} );
	};

	return { postType, meta, setMeta };
};

const PortfolioPanel = ( { meta, setMeta } ) => (
	<PluginDocumentSettingPanel
		name="sekeco-portfolio-details"
		title={ __( 'Work details', 'sekeco' ) }
	>
		<TextControl
			label={ __( 'Client', 'sekeco' ) }
			value={ meta.sekeco_portfolio_client || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_client', value )
			}
		/>
		<TextControl
			label={ __( 'Year', 'sekeco' ) }
			value={ meta.sekeco_portfolio_year || '' }
			onChange={ ( value ) => setMeta( 'sekeco_portfolio_year', value ) }
			help={ __( 'Examples: 2025 or 2023–2025', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Role', 'sekeco' ) }
			value={ meta.sekeco_portfolio_role || '' }
			onChange={ ( value ) => setMeta( 'sekeco_portfolio_role', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'Project URL', 'sekeco' ) }
			value={ meta.sekeco_portfolio_url || '' }
			onChange={ ( value ) => setMeta( 'sekeco_portfolio_url', value ) }
		/>
		<TextControl
			label={ __( 'Services', 'sekeco' ) }
			value={ meta.sekeco_portfolio_services || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_services', value )
			}
			help={ __( 'Comma separated: Branding, Web, Mobile', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Industry', 'sekeco' ) }
			value={ meta.sekeco_portfolio_industry || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_industry', value )
			}
		/>
		<TextControl
			label={ __( 'Duration', 'sekeco' ) }
			value={ meta.sekeco_portfolio_duration || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_duration', value )
			}
			help={ __( 'Example: 3 months', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Tech stack', 'sekeco' ) }
			value={ meta.sekeco_portfolio_stack || '' }
			onChange={ ( value ) => setMeta( 'sekeco_portfolio_stack', value ) }
			help={ __( 'Comma separated: Nuxt, Supabase', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Team', 'sekeco' ) }
			value={ meta.sekeco_portfolio_team || '' }
			onChange={ ( value ) => setMeta( 'sekeco_portfolio_team', value ) }
			help={ __(
				'Comma separated names. A name that matches a Team entry links to it.',
				'sekeco'
			) }
		/>
		<TextareaControl
			label={ __( 'Outcome', 'sekeco' ) }
			value={ meta.sekeco_portfolio_outcome || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_outcome', value )
			}
			help={ __(
				'One or two sentences about the result, in your own words.',
				'sekeco'
			) }
		/>
		<TextControl
			type="url"
			label={ __( 'Case study URL', 'sekeco' ) }
			value={ meta.sekeco_portfolio_case_study || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_portfolio_case_study', value )
			}
		/>
	</PluginDocumentSettingPanel>
);

const ProductPanel = ( { meta, setMeta } ) => (
	<PluginDocumentSettingPanel
		name="sekeco-product-details"
		title={ __( 'Portfolio details', 'sekeco' ) }
	>
		<SelectControl
			label={ __( 'Type', 'sekeco' ) }
			value={ meta.sekeco_product_type || '' }
			options={ [
				{ label: __( 'Not set', 'sekeco' ), value: '' },
				...(
					window.sekecoProductTypes || [
						'SaaS',
						'Plugin',
						'Tool',
						'Library',
						'App',
						'Template',
						'Other',
					]
				).map( ( type ) => ( { label: type, value: type } ) ),
			] }
			onChange={ ( value ) => setMeta( 'sekeco_product_type', value ) }
			help={ __(
				'Used for the filter on the Portfolio page.',
				'sekeco'
			) }
		/>
		<TextControl
			type="url"
			label={ __( 'Project URL', 'sekeco' ) }
			value={ meta.sekeco_product_url || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_url', value ) }
		/>
		<TextControl
			label={ __( 'Status', 'sekeco' ) }
			value={ meta.sekeco_product_status || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_status', value ) }
			help={ __( 'Examples: Live, Private beta, Open source', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Stack', 'sekeco' ) }
			value={ meta.sekeco_product_stack || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_stack', value ) }
			help={ __( 'Examples: TypeScript, Supabase', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Year', 'sekeco' ) }
			value={ meta.sekeco_product_year || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_year', value ) }
		/>
		<TextControl
			label={ __( 'Repository', 'sekeco' ) }
			value={ meta.sekeco_product_repo || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_repo', value ) }
		/>
		<TextControl
			label={ __( 'Version', 'sekeco' ) }
			value={ meta.sekeco_product_version || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_version', value ) }
			help={ __( 'Example: 2.1.0', 'sekeco' ) }
		/>
		<TextControl
			label={ __( 'Platform', 'sekeco' ) }
			value={ meta.sekeco_product_platform || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_product_platform', value )
			}
			help={ __( 'Comma separated: Web, WordPress, CLI', 'sekeco' ) }
		/>
		<TextControl
			type="url"
			label={ __( 'Documentation URL', 'sekeco' ) }
			value={ meta.sekeco_product_docs || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_docs', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'Demo URL', 'sekeco' ) }
			value={ meta.sekeco_product_demo || '' }
			onChange={ ( value ) => setMeta( 'sekeco_product_demo', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'Download URL', 'sekeco' ) }
			value={ meta.sekeco_product_download || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_product_download', value )
			}
		/>
		<TextControl
			type="url"
			label={ __( 'Changelog URL', 'sekeco' ) }
			value={ meta.sekeco_product_changelog || '' }
			onChange={ ( value ) =>
				setMeta( 'sekeco_product_changelog', value )
			}
		/>
	</PluginDocumentSettingPanel>
);

const TeamPanel = ( { meta, setMeta } ) => (
	<PluginDocumentSettingPanel
		name="sekeco-team-details"
		title={ __( 'Team details', 'sekeco' ) }
	>
		<TextControl
			label={ __( 'Role', 'sekeco' ) }
			value={ meta.sekeco_team_role || '' }
			onChange={ ( value ) => setMeta( 'sekeco_team_role', value ) }
		/>
		<TextControl
			type="email"
			label={ __( 'Email', 'sekeco' ) }
			value={ meta.sekeco_team_email || '' }
			onChange={ ( value ) => setMeta( 'sekeco_team_email', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'GitHub', 'sekeco' ) }
			value={ meta.sekeco_team_github || '' }
			onChange={ ( value ) => setMeta( 'sekeco_team_github', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'LinkedIn', 'sekeco' ) }
			value={ meta.sekeco_team_linkedin || '' }
			onChange={ ( value ) => setMeta( 'sekeco_team_linkedin', value ) }
		/>
		<TextControl
			type="url"
			label={ __( 'Instagram', 'sekeco' ) }
			value={ meta.sekeco_team_instagram || '' }
			onChange={ ( value ) => setMeta( 'sekeco_team_instagram', value ) }
		/>
	</PluginDocumentSettingPanel>
);

export default function MetaPanels() {
	const { postType, meta, setMeta } = usePostMeta();

	if ( 'portfolio' === postType ) {
		return <PortfolioPanel meta={ meta } setMeta={ setMeta } />;
	}

	if ( 'product' === postType ) {
		return <ProductPanel meta={ meta } setMeta={ setMeta } />;
	}

	if ( 'team' === postType ) {
		return <TeamPanel meta={ meta } setMeta={ setMeta } />;
	}

	return null;
}
