/**
 * External imports
 */
import PropTypes from 'prop-types';

/**
 * generates an html <label> tag
 *
 * @function
 * @param {string} label
 * @param {string} htmlFor
 * @param {string} htmlClass
 * @param {boolean} required
 * @param {boolean} strong
 * @param {Object} children
 * @param {Object} attributes
 * @return {string} rendered form label
 */
export const InputLabel = ( {
	label,
	htmlFor,
	htmlClass = '',
	required = false,
	strong = true,
	children,
	...attributes
} ) => {
	// add asterisk to label but not if it is for a checkbox or radio button
	const asterisk = required && children === undefined ?
		( <span className="required">*</span> ) :
		'';
	label = strong ? ( <strong>{ label }</strong> ) : label;
	htmlClass = htmlClass ? htmlClass : 'col-form-label';
	htmlClass += required ? ' required' : '';
	return (
		<label htmlFor={ htmlFor } className={ htmlClass } { ...attributes }>
			{ children }
			{ label }{ asterisk }
		</label>
	);
};

InputLabel.propTypes = {
	label: PropTypes.oneOfType( [
		PropTypes.number,
		PropTypes.string,
	] ).isRequired,
	htmlFor: PropTypes.string.isRequired,
	htmlClass: PropTypes.string,
	required: PropTypes.bool,
	strong: PropTypes.bool,
	children: PropTypes.oneOfType( [
		PropTypes.object,
		PropTypes.arrayOf( PropTypes.object ),
	] ),
};
