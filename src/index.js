import { createRoot } from "react-dom/client";
import EasyElementsForGutenbergApp from './app';
import 'antd/dist/reset.css';
import '../assets/css/style.css';
import '../assets/css/editor.css';
import '../assets/css/template-importer.css';
const container = document.getElementById('eelfg-dashboard');
if (container) {
    const root = createRoot(container);
    root.render(<EasyElementsForGutenbergApp initialTab={container.dataset.initialTab} />);
}