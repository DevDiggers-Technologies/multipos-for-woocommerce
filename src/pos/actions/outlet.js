import database from '../services/database';
import { getAssignedOutlets, getPosConfig } from '../services/runtime';

import { OUTLET } from '../state/action-types';
export { OUTLET };
const getOutletStore = () => database.table( 'outlet' );
const getStoredOutlet = outletRecords => Array.isArray( outletRecords ) && outletRecords.length > 0 ? outletRecords[0] : {};
const hasOutletIdentifier = outletId => outletId !== undefined && outletId !== null && outletId !== '';

export const setOutlet = outlet => {
    return {
        type: OUTLET,
        outlet
    }
};

const getResolvedOutlet = outletId => {
    const outlet = getAssignedOutlets().find( assignedOutlet => String( assignedOutlet.id ) === String( outletId ) );

    if ( ! outlet ) {
        return null;
    }

    return {
        ...outlet,
        inventory_type: outlet.inventory_type || getPosConfig().inventory_type,
    };
};

export const setCurrentOutlet = ( outletId = '' ) => dispatch => {
    return getOutletStore().toArray().then( outletRecords => {
        if ( hasOutletIdentifier( outletId ) ) {
            const outlet = getResolvedOutlet( outletId );

            if ( ! outlet ) {
                dispatch( setOutlet( {} ) );
                return {};
            }

            return getOutletStore().put( outlet ).then( () => {
                dispatch( setOutlet( outlet ) );
                return outlet;
            });
        }

        const storedOutlet = getStoredOutlet( outletRecords );

        if ( Object.keys( storedOutlet ).length ) {
            dispatch( setOutlet( storedOutlet ) );
            return storedOutlet;
        }

        dispatch( setOutlet( {} ) );
        return {};
    } );
};
