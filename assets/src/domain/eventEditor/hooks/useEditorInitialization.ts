import useInitQueries from '../services/apollo/initialization/useInitQueries';
import useI18nData from '../services/apollo/initialization/useI18nData';
import { setLocaleData } from '@wordpress/i18n';
import { useEntityActionMenuSubscription } from '../ui/entityActionMenu';

const useEditorInitialization = (): void => {
	// init i18n
	const localeData = useI18nData();
	setLocaleData(localeData);

	// set menu subscription
	useEntityActionMenuSubscription();

	// Fire initial queries
	useInitQueries();
};

export default useEditorInitialization;
