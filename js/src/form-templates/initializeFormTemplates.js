/**
 * Internal dependencies
 */
import { initializeElements } from './elements';
import { initializePageState } from './shared';
import { setupInitialView, initializeModal } from './ui';
import { buildCategorizedTemplates, maybeAddApplicationTemplates } from './templates';
import { addEventListeners } from './events';

/**
 * Initializes form templates.
 *
 * @return {void}
 */
function initializeFormTemplates() {
	initializeElements();
	initializePageState();

	maybeAddApplicationTemplates();

	initializeModal();

	// Generate a categorized list of templates
	buildCategorizedTemplates();

	// Set up the initial view, including any required DOM manipulations for proper presentation
	setupInitialView();
	addEventListeners();
}

export default initializeFormTemplates;
