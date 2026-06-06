import database from './database';

export const POS_LICENSE_CACHE_KEY = 'ddwcposLicenseCheck';

const CACHE_GROUPS = {
    outletData: [
        'temp',
        'outlet',
        'customers',
        'cart',
        'transactions',
        'coupon',
        'fees',
        'products',
        'taxes',
        'categories',
        'countries_and_states',
        'tables',
        'settings',
    ],
    currentOutletData: [
        'temp',
        'customers',
        'cart',
        'transactions',
        'coupon',
        'fees',
        'products',
        'taxes',
        'categories',
        'countries_and_states',
        'tables',
        'settings',
    ],
    switchOutletData: [
        'temp',
        'outlet',
        'customers',
        'cart',
        'transactions',
        'coupon',
        'products',
        'taxes',
        'categories',
        'countries_and_states',
        'tables',
        'settings',
    ],
};
const getCacheTables = group => CACHE_GROUPS[ group ] || CACHE_GROUPS.outletData;
const getUniqueTableNames = tables => Array.from( new Set( tables ) );

const clearTables = tables => Promise.all(
    getUniqueTableNames( tables ).map( tableName => database.table( tableName ).clear() )
);

const removeSyncedOrders = () => database.table( 'orders' ).where( 'order_type' ).equals( 'online' ).delete();
const clearLicenseCache = () => localStorage.removeItem( POS_LICENSE_CACHE_KEY );

export const clearPosCache = ( group = 'outletData' ) => {
    clearLicenseCache();

    return clearTables( getCacheTables( group ) ).then( () => removeSyncedOrders() );
};
