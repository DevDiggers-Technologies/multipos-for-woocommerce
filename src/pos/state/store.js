import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { composeWithDevTools } from 'redux-devtools-extension';
import { createRootReducer, getDefaultStates } from './reducers';

import { createStateSyncMiddleware } from 'redux-state-sync';

const defaultStates = getDefaultStates();
const rootReducer = createRootReducer();
const stateSyncConfig = {};
const middlewares = [ createStateSyncMiddleware( stateSyncConfig ) ];
const createStoreEnhancer = () => composeWithDevTools( applyMiddleware( thunk, ...middlewares ) );

const store = createStore(
    rootReducer,
    defaultStates,
    createStoreEnhancer()
);

export default store;
