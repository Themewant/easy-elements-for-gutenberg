import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

const eelfgIcon = (
	<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect x="14" y="24" width="18" height="14" rx="3" fill="#a216ff" />
		<rect x="40" y="27" width="46" height="8" rx="4" fill="#a216ff" opacity="0.6" />
		<rect x="14" y="46" width="18" height="14" rx="3" fill="#a216ff" />
		<rect x="40" y="49" width="46" height="8" rx="4" fill="#a216ff" opacity="0.6" />
		<rect x="14" y="68" width="18" height="14" rx="3" fill="#a216ff" />
		<rect x="40" y="71" width="46" height="8" rx="4" fill="#a216ff" opacity="0.6" />
	</svg>
);

registerBlockType(metadata.name, {
	icon: eelfgIcon,
	edit: Edit,
	save,
});
