import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

const eelfgIcon = (
	<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
		<text x="6" y="62" fontSize="34" fontWeight="700" fill="#a216ff">01</text>
		<rect x="44" y="30" width="40" height="40" rx="10" fill="#a216ff" opacity="0.55" />
	</svg>
);

registerBlockType(metadata.name, {
	icon: eelfgIcon,
	edit: Edit,
	save,
});
