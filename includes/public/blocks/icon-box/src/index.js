import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

const eelfgIcon = (
	<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect x="14" y="10" width="72" height="80" rx="10" fill="#a216ff" opacity="0.12" />
		<rect x="34" y="20" width="32" height="32" rx="8" fill="#a216ff" />
		<rect x="28" y="60" width="44" height="6" rx="3" fill="#a216ff" />
		<rect x="34" y="72" width="32" height="5" rx="2.5" fill="#a216ff" opacity="0.6" />
	</svg>
);

registerBlockType(metadata.name, {
	icon: eelfgIcon,
	edit: Edit,
	save,
});
