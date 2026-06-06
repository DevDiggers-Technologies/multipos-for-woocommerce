export const getPosObject = () => (
    typeof window !== 'undefined'
    && typeof window.ddwcposPOSObj === 'object'
    && window.ddwcposPOSObj
        ? window.ddwcposPOSObj
        : {}
);

export const getPosConfig = () => getPosObject().ddwcpos_configuration || {};

export const getPosApi = () => getPosObject().API || {};

export const getCurrentPosUser = () => getPosObject().user || {};

export const getPosConfigValue = ( key, fallback = '' ) => getPosConfig()[ key ] || fallback;

export const getAssignedOutlets = () => (
    Array.isArray( getPosObject().assignedOutlets ) ? getPosObject().assignedOutlets : []
);

export const getTaxType = () => getPosObject().tax_type || '';

export const isTaxEnabled = () => getPosObject().tax_enabled === 'yes';

export const getWeightUnit = () => getPosObject().weight_unit || '';

export const getPosLogoUrl = () => getPosConfig().logo_url || '';

export const getCurrentPosDate = () => getPosObject().current_date || '';
