import { store } from 'react-notifications-component';

const getNotificationDismiss = duration => ( {
    duration,
    pauseOnHover: true,
} );
const buildNotificationPayload = ( {
    title,
    message,
    type,
    duration,
    container,
} ) => ( {
    title,
    message,
    type,
    insert : 'top',
    container,
    dismiss: getNotificationDismiss( duration ),
} );

export const notify = ( {
    title,
    message,
    type = 'info',
    duration = 3000,
    container = 'top-right',
} ) => {
    if ( ! message ) {
        return;
    }

    store.addNotification( buildNotificationPayload( {
        title,
        message,
        type,
        duration,
        container,
    } ) );
};
