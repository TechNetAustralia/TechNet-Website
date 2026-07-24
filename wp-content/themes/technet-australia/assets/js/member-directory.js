/**
 * Live search filter for the member directory — vanilla JS equivalent of
 * the `members.filter(...)` live filter in
 * ui_kits/member-directory/index.html's React demo. No framework at
 * runtime; just toggles row visibility as the search box changes.
 */
( function () {
	'use strict';

	var input = document.getElementById( 'technet-directory-search' );
	var list = document.getElementById( 'technet-directory-list' );

	if ( ! input || ! list ) {
		return;
	}

	var rows = list.querySelectorAll( '.technet-directory-row' );

	input.addEventListener( 'input', function () {
		var query = input.value.trim().toLowerCase();
		var visibleCount = 0;

		rows.forEach( function ( row ) {
			var match = row.getAttribute( 'data-search' ).indexOf( query ) !== -1;
			row.style.display = match ? '' : 'none';
			if ( match ) {
				visibleCount += 1;
			}
		} );

		var emptyState = list.querySelector( '.technet-directory-empty--search' );
		if ( 0 === visibleCount ) {
			if ( ! emptyState ) {
				emptyState = document.createElement( 'div' );
				emptyState.className = 'technet-directory-empty technet-directory-empty--search';
				emptyState.textContent = 'No members match that search.';
				list.appendChild( emptyState );
			}
		} else if ( emptyState ) {
			emptyState.remove();
		}
	} );
} )();
