/**
 * Provide functions to consistently generate class names.
 */
export function getClassName( name: string, existingClassName?: string ): string {
	return combineClassNames( existingClassName, `rdb-${ toKebabCase( name ) }` );
}

/**
 * Combine class names, filtering out any falsy values.
 */
export function combineClassNames( ...classNames: ( string | undefined )[] ): string {
	return classNames.filter( Boolean ).join( ' ' );
}

/**
 * Convert a string to kebab-case.
 */
export function toKebabCase( str: string ): string {
	return str.replace( /[^a-zA-Z\d\u00C0-\u00FF]/g, '-' ).toLowerCase();
}

/**
 * Convert a string to title case.
 */
export function toTitleCase( str: string ): string {
	return str.replace( /\w\S*/g, txt => {
		return txt.charAt( 0 ).toUpperCase() + txt.substring( 1 ).toLowerCase();
	} );
}

export const slugToTitleCase = ( slug: string ): string => {
	return slug.replace( /-/g, ' ' ).replace( /\b\w/g, char => char.toUpperCase() );
};

/**
 * Casts a string to JSON
 * @param value string to cast
 * @returns parsed JSON or null
 */
export function safeParseJSON< T = unknown >( value: unknown ): T | null {
	if ( 'undefined' === typeof value || null === value ) {
		return null;
	}

	if ( 'string' === typeof value && value.trim().length === 0 ) {
		return null;
	}

	if ( 'string' === typeof value ) {
		try {
			return JSON.parse( value ) as T;
		} catch ( error ) {
			return null;
		}
	}

	return null;
}
