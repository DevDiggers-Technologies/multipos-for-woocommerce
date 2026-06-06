import Dexie from 'dexie';

const database = new Dexie( 'ddwcpos' );
const getTempTable = () => database.temp;

const tables = {
    temp                : 'id',
    outlet              : 'id',
    customers           : 'id, first_name, last_name, username, email, phone, display_name',
    orders              : 'id, order_type',
    cart                : 'id, active_cart',
    transactions        : 'id, date',
    coupon              : 'id, cart_id',
    fees                : 'id, cart_id',
    products            : 'product_id, type, barcode_init',
    taxes               : 'id',
    categories          : 'id',
    countries_and_states: 'base_country',
    tables              : 'slug',
    settings            : 'id',
}

database.version(1).stores( tables );

const ensureTempBootstrapRecord = () => (
    getTempTable().get( 0 ).then( currentCartRecord => {
        if ( currentCartRecord ) {
            return currentCartRecord;
        }

        return getTempTable().put( { id: 0 } ).then( () => getTempTable().get( 0 ) );
    } )
);

ensureTempBootstrapRecord().catch( () => null );

export default database;
