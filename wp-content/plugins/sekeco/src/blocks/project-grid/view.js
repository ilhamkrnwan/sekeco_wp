/**
 * Filter for the Project grid: category or type buttons, and for the catalog
 * layout a search box. Both narrow the same list; an empty result says so.
 */
document.querySelectorAll( '[data-sk-filter]' ).forEach( ( grid ) => {
	const buttons = grid.querySelectorAll( '.sk-filter__btn' );
	const items = grid.querySelectorAll( '.sk-projects__item' );
	const search = grid.querySelector( '[data-sk-search]' );
	const empty = grid.querySelector( '[data-sk-empty]' );
	let key = '*';

	const apply = () => {
		const query = search ? search.value.trim().toLowerCase() : '';
		let shown = 0;

		items.forEach( ( item ) => {
			const show =
				( '*' === key ||
					( item.dataset.terms || '' )
						.split( ' ' )
						.includes( key ) ) &&
				( '' === query ||
					item.textContent.toLowerCase().includes( query ) );

			item.hidden = ! show;
			shown += show ? 1 : 0;
		} );

		if ( empty ) {
			empty.hidden = shown > 0;
		}
	};

	buttons.forEach( ( button ) => {
		button.addEventListener( 'click', () => {
			key = button.dataset.filter;

			buttons.forEach( ( other ) => {
				const active = other === button;

				other.classList.toggle( 'is-active', active );
				other.setAttribute( 'aria-pressed', String( active ) );
			} );

			apply();
		} );
	} );

	if ( search ) {
		search.addEventListener( 'input', apply );
	}
} );
